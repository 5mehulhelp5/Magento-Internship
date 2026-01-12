<?php
namespace Perspective\ErpImport\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Perspective\ErpImport\Logger\ProductImportLogger;

//test class for cron debug
class ImportProducts implements ObserverInterface
{
    protected $logger;

    public function __construct(ProductImportLogger $logger)
    {
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        //поменять адрес лога

        $dir = BP . '/var/import/archive/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $importFile = BP . '/var/import/products.csv';
        $batchSize = 100;
        $batch = [];
        $rowNumber = 0;

        $this->logger->info("START IMPORT");

        if (!file_exists($importFile)) {
            $this->logger->info("CSV file not found: {$importFile}");
            return;
        }

        if (($handle = fopen($importFile, 'r')) !== false) {
            $headers = fgetcsv($handle);

            while (($data = fgetcsv($handle)) !== false) {
                $rowNumber++;
                $row = array_combine($headers, $data);

                // Валидация -- вынести в отдельный файл -- функция rowIsValid
                if ($row['sku'] === '' || $row['status'] === '' || $row['price'] === '') {
                    $this->logger->info("Row {$rowNumber}: invalid data, SKU={$row['sku']}");
                    continue;
                }

                // Добавляем в batch
                $batch[] = $row;

                // Если набрали 100 строк, отправляем в Bulk API
                if (count($batch) === $batchSize) {
                    $test = 1;//bulk
                    $batch = []; // очищаем
                }
            }

            // Остаток меньше 100
            if (!empty($batch)) {
                //$this->processBatch($batch); bulk service
                $test = 1;
            }

            fclose($handle);
        }

        //bulk service

    }
}

/*
<?php
namespace Perspective\ErpImport\Model;

use Psr\Log\LoggerInterface;
use Magento\Framework\HTTP\Client\Curl;

class BulkService
{
    protected $logger;
    protected $curl;
    protected $apiUrl;
    protected $token;

    public function __construct(
        LoggerInterface $logger,
        Curl $curl,
        string $apiUrl,
        string $token
    ) {
        $this->logger = $logger;
        $this->curl = $curl;
        $this->apiUrl = $apiUrl;
        $this->token = $token;
    }

    public function sendBatch(array $batch): array
    {
        $payload = ['sourceItems' => []];

        foreach ($batch as $item) {
            $payload['sourceItems'][] = [
                'sku' => $item['sku'],
                'status' => $item['status'] === 'enabled' ? 1 : 0,
                'price' => $item['price']
            ];
        }

        try {
            $this->curl->addHeader("Authorization", "Bearer " . $this->token);
            $this->curl->addHeader("Content-Type", "application/json");
            $this->curl->post($this->apiUrl . '/rest/default/async/bulk/V1/inventory/source-items', json_encode($payload));

            $response = json_decode($this->curl->getBody(), true);
            $bulkId = $response['bulk_uuid'] ?? null;

            if (!$bulkId) {
                $this->logger->error('Bulk API did not return UUID', ['batch' => $batch]);
                return ['success' => [], 'error' => array_column($batch, 'sku')];
            }

            $this->logger->info("Bulk request sent, UUID: {$bulkId}");
            return ['success' => array_column($batch, 'sku'), 'bulk_uuid' => $bulkId];

        } catch (\Exception $e) {
            $this->logger->error('Bulk API error: ' . $e->getMessage(), ['batch' => $batch]);
            return ['success' => [], 'error' => array_column($batch, 'sku')];
        }
    }
}

*/