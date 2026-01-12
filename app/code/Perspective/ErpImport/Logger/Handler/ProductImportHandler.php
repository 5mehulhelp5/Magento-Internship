<?php
namespace Perspective\ErpImport\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;
use Magento\Framework\Filesystem\Driver\File;
use Monolog\Formatter\LineFormatter;

class ProductImportHandler extends Base
{

    protected $loggerType = Logger::INFO;

    public function __construct(File $filesystem)
    {
        $this->fileName = '/var/import/archive/product_import_' . date('Y-m-d') . '.log';
        parent::__construct($filesystem);

        $formatter = new LineFormatter("[%datetime%] %message%\n", "Y-m-d H:i:s", true, true);
        $this->setFormatter($formatter);
    }
}
