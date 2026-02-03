<?php
namespace Perspective\PopularProductList\Cron;

use Psr\Log\LoggerInterface;
use Perspective\PopularProductList\Service\PopularProductsManagement;
use Magento\Framework\App\Cache\Frontend\Pool;
use Throwable;
use Zend_Cache;
use Perspective\PopularProductList\Service\ConfigData;

class RefreshPopularProducts
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var PopularProductsManagement
     */
    protected $popularProductsManager;
    /**
     * @var Pool
     */
    protected $frontendCachePool;
    /**
     * @var ConfigData
     */
    protected $configDataService;

    /**
     * @param LoggerInterface $logger
     * @param PopularProductsManagement $popularProductsManager
     * @param Pool $frontendCachePool
     * @param ConfigData $configDataService
     */
    public function __construct(
        LoggerInterface $logger,
        PopularProductsManagement $popularProductsManager,
        Pool $frontendCachePool,
        ConfigData $configDataService,
    ) {
        $this->logger = $logger;
        $this->popularProductsManager = $popularProductsManager;
        $this->frontendCachePool = $frontendCachePool;
        $this->configDataService = $configDataService;
    }

    /**
     * Update popular products and clean cache for pages where they are shown
     *
     * @return void
     */
    public function execute(): void
    {
        // skip cron if module disabled
        if (!$this->configDataService->isModuleEnabled()) {
            return;
        }

        // update top
        try {
            $this->popularProductsManager->refreshTopProducts();
            $this->logger->notice(__('Popular products top successfully updated.'));
        } catch (Throwable $e) {
            $this->logger->error(__('Error refreshing popular products: %1', $e->getMessage()));
            return;
        }

        // clean FPC on pages with popular products top
        foreach ($this->frontendCachePool as $frontendCache) {
            try {
                $frontendCache->clean(
                    Zend_Cache::CLEANING_MODE_MATCHING_TAG,
                    ['PERSPECTIVE_PRODUCT_TOP_LIST']
                );
            } catch (Throwable $e) {
                $this->logger->error(__('Error cleaning FPC: %1', $e->getMessage()));
            }
        }
        $this->logger->notice(__('FPC cleaned on pages with popular-product-slider'));
    }
}
