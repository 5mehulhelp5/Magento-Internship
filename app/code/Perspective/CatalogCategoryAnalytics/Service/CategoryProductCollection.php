<?php
namespace Perspective\CatalogCategoryAnalytics\Service;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class CategoryProductCollection
{
    protected array $collectionsCache = [];

    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @param CollectionFactory $productCollectionFactory
     */
    public function __construct(
        CollectionFactory $productCollectionFactory
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * Get product collection for a category
     * Filtered by visibility, status, stock, categories
     *
     * @param Category $category
     * @param bool $stockFilter
     * @return Collection
     */
    public function getCategoryProductCollection(Category $category, bool $stockFilter): Collection
    {
        // select type of collection by stock filter (all or only in stock)
        $collectionType = 'all';
        if ($stockFilter) {
            $collectionType = 'in_stock';
        }

        // if collection set already - return cached collection
        if (isset($this->collectionsCache[$collectionType])) {
            return $this->collectionsCache[$collectionType];
        }

        //create collection
        $collection = $this->productCollectionFactory->create();
        $collection->setStoreId($category->getStoreId());
        // in_stock filter
        if (!$stockFilter) {
            $collection->setFlag('has_stock_status_filter', false);
        }

        $collection->addAttributeToSelect(['entity_id', 'price']);

        //category filter (recursive for configurable products)
        $categoryIds = $category->getChildren(true);
        if (!$categoryIds) {
            $categoryIds = [$category->getId()];
        }
        $collection->addCategoriesFilter(['in' => $categoryIds]);

        // filter by visibility and status(enabled)
        $collection->addAttributeToFilter('status', 1);
        $collection->addAttributeToFilter('visibility', 4);

        // load (without that not work stock filter)
        $collection->load();

        // caching collection
        $this->collectionsCache[$collectionType] = $collection;
        return $collection;
    }
}
