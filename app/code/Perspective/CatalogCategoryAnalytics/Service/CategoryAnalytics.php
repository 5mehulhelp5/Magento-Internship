<?php
namespace Perspective\CatalogCategoryAnalytics\Service;

use Magento\Catalog\Model\Category;
use Magento\Framework\App\CacheInterface;
use Magento\Customer\Model\Session;
use Perspective\CatalogCategoryAnalytics\Service\CategoryProductCollection;

class CategoryAnalytics
{
    /**
     * @var CacheInterface
     */
    protected $cache;
    /**
     * @var Session
     */
    protected $customerSession;
    /**
     * @var CategoryProductCollection
     */
    protected $productCollectionService;

    /**
     * @param CacheInterface $cache
     * @param Session $customerSession
     * @param CategoryProductCollection $productCollectionService
     */
    public function __construct(
        CacheInterface $cache,
        Session $customerSession,
        CategoryProductCollection $productCollectionService
    ) {
        $this->cache = $cache;
        $this->customerSession = $customerSession;
        $this->productCollectionService = $productCollectionService;
    }

    /**
     * Collect analytics data for a category
     * Uses cache if available, otherwise collect values from product collections
     * Cache key depends on category, store, and customer group to preserve personalization
     *
     * @param Category $category
     * @return array
     */
    public function getAnalytics(Category $category): array
    {
        // cache_id => CATEGORY_ANALYTICS_categoryId_storeId_customerGroupId
        $cacheId = sprintf(
            'CATEGORY_ANALYTICS_%d_%d_%d',
            $category->getId(),
            $category->getStoreId(),
            $this->customerSession->getCustomerGroupId()
        );

        // return cached analytics data if set
        $data = $this->cache->load($cacheId);
        if ($data) {
            return unserialize($data);
        }

        // collect data
        $data = [
            'total_count' => $this->getCategorySize($category, false),
            'average_price' => $this->getCategoryAveragePrice($category),
            'in_stock_count' => $this->getCategorySize($category, true)
        ];

        // caching data
        $this->cache->save(serialize($data), $cacheId, ['category_analytics'], 1800);

        return $data;
    }

    /**
     * Get size of category product collection
     *
     * @param Category $category
     * @param bool $stockFilter
     * @return int
     */
    protected function getCategorySize(Category $category, bool $stockFilter): int
    {
        return $this->productCollectionService->getCategoryProductCollection($category, $stockFilter)->getSize();
    }

    /**
     * Get average price from category product collection
     *
     * @param Category $category
     * @return float
     */
    protected function getCategoryAveragePrice(Category $category): float
    {
        $prices = $this->productCollectionService->getCategoryProductCollection($category, true)->getColumnValues('price');
        if (empty($prices)) {
            return 0.0;
        }
        return round(array_sum($prices)/count($prices), 2);
    }
}
