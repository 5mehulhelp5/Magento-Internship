<?php

namespace Perspective\PopularProductList\Service;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Perspective\PopularProductList\Service\PopularProductsCollector;
use Perspective\PopularProductList\Model\PopularProductFactory;
use Perspective\PopularProductList\Model\ResourceModel\PopularProduct as ResourceModel;
use Perspective\PopularProductList\Model\ResourceModel\PopularProduct\CollectionFactory as PopularCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Zend_Db_Expr;
use Perspective\PopularProductList\Service\ConfigData;



class PopularProductsManagement
{
    /**
     * @var \Perspective\PopularProductList\Service\PopularProductsCollector
     */
    protected $popularProductsCollector;
    /**
     * @var PopularProductFactory
     */
    protected $popularProductFactory;
    /**
     * @var ResourceModel
     */
    protected $resourceModel;
    /**
     * @var PopularCollectionFactory
     */
    protected $popularCollectionFactory;
    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;
    /**
     * @var \Perspective\PopularProductList\Service\ConfigData
     */
    protected $configDataService;

    /**
     * @param \Perspective\PopularProductList\Service\PopularProductsCollector $popularProductsCollector
     * @param PopularProductFactory $popularProductFactory
     * @param ResourceModel $resourceModel
     * @param PopularCollectionFactory $popularCollectionFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param \Perspective\PopularProductList\Service\ConfigData $configDataService
     */
    public function __construct(
        PopularProductsCollector $popularProductsCollector,
        PopularProductFactory $popularProductFactory,
        ResourceModel $resourceModel,
        PopularCollectionFactory $popularCollectionFactory,
        ProductCollectionFactory $productCollectionFactory,
        ConfigData $configDataService
    ) {
        $this->popularProductsCollector = $popularProductsCollector;
        $this->popularProductFactory = $popularProductFactory;
        $this->resourceModel = $resourceModel;
        $this->popularCollectionFactory = $popularCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->configDataService = $configDataService;
    }

    /**
     * Update popular products ranks
     *
     * @return void
     */
    public function refreshTopProducts(): void
    {
        $topProducts = $this->popularProductsCollector->getTopProductStats();
        $rank = 1;
        foreach ($topProducts as $productId => $count) {
            $model = $this->popularProductFactory->create();

            $this->resourceModel->load($model, $rank);

            $model->setData('rank', $rank);
            $model->setData('product_id', $productId);
            $model->setData('orders_count', $count);

            $this->resourceModel->save($model); //exc
            $rank++;
        }
        $this->deleteInvalidRanks();
    }

    /**
     * Get popular products array sorted by rank
     *
     * @return array
     */
    public function getTopProducts(): array
    {
        // get sorted product ids from popular products table
        $popularCollection = $this->popularCollectionFactory->create();
        $productIds = $popularCollection->getColumnValues('product_id');

        // initialize product collection filtered by collected popular ids
        $productCollection = $this->productCollectionFactory->create()
            ->addIdFilter($productIds)
            ->addAttributeToSelect('*');

        // save rank sorting for products collection
        if (!empty($productIds)) {
            $productCollection->getSelect()->order(new Zend_Db_Expr('FIELD(entity_id,' . implode(',', $productIds).')'));
        }

        return $productCollection->getItems();
    }

    /**
     * Remove records from popular products table if they rank greater than limit
     * (If limit changed)
     *
     * @return void
     */
    public function deleteInvalidRanks(): void
    {
        $limit = $this->configDataService->getDisplayCount();
        $collection = $this->popularCollectionFactory->create();
        $collection->addFieldToFilter('rank', ['gt' => $limit]);

        foreach ($collection as $record) {
            $record->delete();
        }
    }
}
