<?php
namespace Perspective\PopularProductList\Service;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;
use Perspective\PopularProductList\Model\PopularProduct;
use Perspective\PopularProductList\Service\ConfigData;
use Perspective\PopularProductList\Model\PopularProductFactory;
use Perspective\PopularProductList\Model\ResourceModel\PopularProduct as ResourceModel;


class PopularProductsCollector
{
    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;
    /**
     * @var OrderItemCollectionFactory
     */
    protected $orderItemCollectionFactory;
    /**
     * @var \Perspective\PopularProductList\Service\ConfigData
     */
    protected $configDataService;
    /**
     * @var PopularProductFactory
     */
    protected $popularProductFactory;
    /**
     * @var ResourceModel
     */
    protected $resourceModel;

    /**
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param OrderItemCollectionFactory $orderItemCollectionFactory
     * @param \Perspective\PopularProductList\Service\ConfigData $configDataService
     * @param PopularProductFactory $popularProductFactory
     * @param ResourceModel $resourceModel
     */
    public function __construct(
        OrderCollectionFactory $orderCollectionFactory,
        OrderItemCollectionFactory $orderItemCollectionFactory,
        ConfigData $configDataService,
        PopularProductFactory $popularProductFactory,
        ResourceModel $resourceModel,
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->configDataService = $configDataService;
        $this->popularProductFactory = $popularProductFactory;
        $this->resourceModel = $resourceModel;
    }

    /**
     * Get a list of product ids and how many times they were ordered
     *
     * @return array [product_id => count]
     */
    private function getProductFrequencyData(): array
    {
        // get order ids filtered by 'complete' or 'processing' status
        $orderCollection = $this->orderCollectionFactory->create()
            ->addFieldToSelect(['entity_id', 'status'])
            ->addFieldToFilter('status', ['in' => ['processing', 'complete']]);
        $orderIds = $orderCollection->getColumnValues('entity_id');

        // get product ids filtered by order ids excluding child items of configurables
        $itemCollection = $this->orderItemCollectionFactory->create()
            ->addFieldToSelect(['product_id'])
            ->addFieldToFilter('order_id', ['in' => $orderIds])
            ->addFieldToFilter('parent_item_id', ['null' => true]); // for products with parent(configurable)
        $productIds = $itemCollection->getColumnValues('product_id');

        // count how many times each product ID appears in the list
        return array_count_values($productIds);
    }

    /**
     * Get the top popular products based on the limit from settings
     *
     * @return array [product_id => count]
     */
    public function getTopProductStats(): array
    {
        $productCounts = $this->getProductFrequencyData();
        if (empty($productCounts)) {
            return [];
        }

        // sort from most popular to least and take only limited amount
        $limit = $this->configDataService->getDisplayCount();
        arsort($productCounts);
        return array_slice($productCounts, 0, $limit, true);
    }
}
