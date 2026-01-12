<?php
namespace Perspective\ErpImport\Cron;

use Perspective\ErpImport\Logger\ProductImportLogger;

class ImportProducts
{
    protected $logger;

    public function __construct(ProductImportLogger $logger)
    {
        $this->logger = $logger;
    }

    public function execute()
    {
        $dir = BP . '/var/import/archive/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $this->logger->info('=== ERP Import started ===');

        $invalidSku = 'erp-123';
        $this->logger->info("Invalid SKU found: {$invalidSku}");

        $batchProcessed = 100;
        $this->logger->info("Batch processed: {$batchProcessed} products");

        $this->logger->info('=== ERP Import finished ===');

        // TODO: здесь будет твой pipeline:
        // 1. открыть CSV построчно
        // 2. формировать batch по 100
        // 3. отправлять Bulk
        // 4. логировать ошибки
        // 5. архивировать файл
    }
}
