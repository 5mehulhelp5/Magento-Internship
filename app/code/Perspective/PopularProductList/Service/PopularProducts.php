<?php
namespace Perspective\PopularProductList\Service;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;
use Perspective\PopularProductList\Service\ConfigData;


class PopularProducts
{
    protected $orderCollectionFactory;
    protected $orderItemCollectionFactory;
    protected $configDataService;

    public function __construct(
        OrderCollectionFactory $orderCollectionFactory,
        OrderItemCollectionFactory $orderItemCollectionFactory,
        ConfigData $configDataService
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->configDataService = $configDataService;
    }

    public function getProductFrequencyData()
    {

        $orderCollection = $this->orderCollectionFactory->create()
            ->addFieldToSelect(['entity_id', 'status'])
            ->addFieldToFilter('status', ['in' => ['processing', 'complete']]);
        $orderIds = $orderCollection->getColumnValues('entity_id');


        $itemCollection = $this->orderItemCollectionFactory->create()
            ->addFieldToSelect(['product_id'])
            ->addFieldToFilter('order_id', ['in' => $orderIds])
            ->addFieldToFilter('parent_item_id', ['null' => true]); // for products with parent(configurable)
        $productIds = $itemCollection->getColumnValues('product_id');

        return array_count_values($productIds);
    }

    public function getTopProductStats()
    {
        $productCounts = $this->getProductFrequencyData();
        if (empty($productCounts)) {
            return [];
        }

        $limit = $this->configDataService->getDisplayCount();

        arsort($productCounts);
        return array_slice($productCounts, 0, $limit, true);

    }

    public function updateTopProducts()
    {

    }
}
