<?php
namespace Perspective\ErpImport\Logger;

use Monolog\Logger as MonologLogger;

class ProductImportLogger extends MonologLogger
{
    /**
     * @param string $name
     * @param array $handlers
     */
    public function __construct(string $name = 'product_import_logger', array $handlers = [])
    {
        parent::__construct($name, $handlers);
    }
}
