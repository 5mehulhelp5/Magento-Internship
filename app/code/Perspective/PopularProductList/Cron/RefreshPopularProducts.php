<?php
namespace Perspective\PopularProductList\Cron;

use Psr\Log\LoggerInterface;
use Perspective\PopularProductList\Service\PopularProductsManagement;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\PageCache\Model\Cache\Type;
use Magento\Framework\App\Cache\TypeListInterface;


class RefreshPopularProducts
{
    protected $logger;
    protected $popularProductsManager;
    protected $cacheManager;
    protected $cachePool;
    protected $cacheTypeList;

    public function __construct(
        LoggerInterface $logger,
        PopularProductsManagement $popularProductsManager,
        CacheInterface $cacheManager,
        Pool $cachePool,
        TypeListInterface $cacheTypeList,
    ) {
        $this->logger = $logger;
        $this->popularProductsManager = $popularProductsManager;
        $this->cacheManager = $cacheManager;
        $this->cachePool = $cachePool;
        $this->cacheTypeList = $cacheTypeList;
    }


    /**
     * Cronjob Description
     *
     * @return void
     */
    public function execute(): void //проблема с кешем
    {
        $this->popularProductsManager->refreshTopProducts();
        $this->logger->critical('Refresh popular products');

        $fpcCache = $this->cachePool->get(Type::TYPE_IDENTIFIER);
        $fpcCache->clean(
            \Zend_Cache::CLEANING_MODE_ALL,
            ['PERSPECTIVE_PRODUCT_TOP_LIST']
        );
        $this->cacheTypeList->invalidate('PERSPECTIVE_PRODUCT_TOP_LIST');
        $this->cacheManager->remove('PERSPECTIVE_PRODUCT_TOP_LIST');
    }
}
