<?php
namespace Perspective\ErpImport\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;
use Magento\Framework\Filesystem\Driver\File;
use Monolog\Formatter\LineFormatter;

class ProductImportHandler extends Base
{
    /**
     * @var int
     */
    protected $loggerType = Logger::INFO;
    /**
     * @param File $filesystem
     */
    public function __construct(File $filesystem)
    {
        //custom log file
        $this->fileName = '/var/log/erp_import/product_import_' . date('Y-m-d') . '.log';
        parent::__construct($filesystem);

        //custom log format
        $formatter = new LineFormatter("[%datetime%] %message%\n", "Y-m-d H:i:s", true, true);
        $this->setFormatter($formatter);
    }
}
