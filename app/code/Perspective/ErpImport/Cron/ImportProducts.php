<?php
namespace Perspective\ErpImport\Cron;

use Perspective\ErpImport\Logger\ProductImportLogger;
use Perspective\ErpImport\Service\BulkManager;
use Perspective\ErpImport\Service\BulkValidator;

class ImportProducts
{
    /**
     * @var ProductImportLogger
     */
    protected $logger;
    /**
     * @var BulkManager
     */
    protected bulkManager $bulkManager;
    /**
     * @var BulkValidator
     */
    protected bulkValidator $bulkValidator;

    /**
     * @param ProductImportLogger $logger
     * @param BulkManager $bulkManager
     * @param BulkValidator $bulkValidator
     */
    public function __construct(
        ProductImportLogger $logger,
        BulkManager         $bulkManager,
        BulkValidator       $bulkValidator,
    )
    {
        $this->logger = $logger;
        $this->bulkManager = $bulkManager;
        $this->bulkValidator = $bulkValidator;
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(): void
    {
        //check and create directories for archive and log
        $archiveDir = BP . '/var/import/archive/';
        if (!is_dir($archiveDir)) {
            mkdir($archiveDir, 0777, true);
        }
        $logDir = BP . '/var/log/erp_import/';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        //start timer
        $startTime = microtime(true);
        $this->logger->info("START IMPORT");

        $importFile = BP . '/var/import/products.csv';
        $batchSize = 100;
        $batch = [];
        $rowNumber = 0;
        $updatedSuccess = 0;
        $updatedFail = 0;

        if (!file_exists($importFile)) {
            //if file not found
            $this->logger->info("CSV file not found: {$importFile}");
        } else {
            if (($handle = fopen($importFile, 'r')) !== false) {
                //get csv headers
                $headers = fgetcsv($handle);
                while (($data = fgetcsv($handle)) !== false) {
                    $rowNumber++;
                    $row = array_combine($headers, $data);

                    //row validation
                    $rowIsValid = $this->bulkValidator->rowIsValid($row);
                    if (!$rowIsValid['valid']) {
                        //skip and log if not valid
                        $this->logger->info("ERROR. [{$rowNumber}]Sku: {$row['sku']}. Reason: {$rowIsValid['message']}");
                        $updatedFail++;
                        continue;
                    }

                    //add row to batch if valid
                    //batch - array of valid rows
                    $batch[] = $row;

                    if (count($batch) === $batchSize) {
                        //if batch reached size limit -> send bulk request and get response
                        $bulkResponse = $this->bulkManager->process($batch);
                        if ($bulkResponse['status'] != 202) {
                            $this->logger->info("Bulk failed, error: " . $bulkResponse['body']['message'] . ' Status: ' . $bulkResponse['status']);
                            $updatedFail += count($batch);
                        } else {
                            $this->logger->info("Bulk scheduled, UUID: " . $bulkResponse['body']['bulk_uuid'] . ' Status: ' . $bulkResponse['status']);
                            $updatedSuccess += count($batch);
                        }
                        $batch = [];
                    }
                }

                //if last batch not reached limit
                if (!empty($batch)) {
                    $bulkResponse = $this->bulkManager->process($batch);
                    if ($bulkResponse['status'] != 202) {
                        $this->logger->info("Bulk failed, error: " . $bulkResponse['body']['message'] . ' Status: ' . $bulkResponse['status']);
                        $updatedFail += count($batch);
                    } else {
                        $this->logger->info("Bulk scheduled, UUID: " . $bulkResponse['body']['bulk_uuid'] . ' Status: ' . $bulkResponse['status']);
                        $updatedSuccess += count($batch);
                    }
                    $batch = [];
                }

                //file close and archive
                fclose($handle);
                $archiveFile = $archiveDir . 'products_' . date('Y-m-d') . '.csv';
                rename($importFile, $archiveFile);
                $this->logger->info("CSV file archived to {$archiveFile}");
            }
        }

        //log summary
        $this->logger->info("IMPORT FINISHED. Successfully updated: {$updatedSuccess}. Failed: {$updatedFail}.");
        //stop timer
        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);
        $this->logger->info("Execution time: {$executionTime} s");
    }
}
