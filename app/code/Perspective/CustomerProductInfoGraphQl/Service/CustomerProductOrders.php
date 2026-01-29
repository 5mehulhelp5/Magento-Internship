<?php

namespace Perspective\CustomerProductInfoGraphQl\Service;

use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Perspective\CustomerProductInfoGraphQl\Service\CurrentCustomer;

class CustomerProductOrders
{
    protected $collectionFactory;
    protected $timezone;
    protected $configurableTypeResourceModel;
    protected $currentCustomerService;

    public function __construct(
        CollectionFactory $collectionFactory,
        TimezoneInterface $timezone,
        Configurable $configurableTypeResourceModel,
        CurrentCustomer $currentCustomerService
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->timezone = $timezone;
        $this->configurableTypeResourceModel = $configurableTypeResourceModel;
        $this->currentCustomerService = $currentCustomerService;
    }




    public function getCustomerOrders(): Collection
    {
        $collection = $this->collectionFactory->create()
            ->addFieldToSelect(['entity_id', 'status', 'created_at'])
            ->addFieldToFilter('customer_id', $this->context->getUserId()) //
            ->addFieldToFilter('status', ['neq' => 'canceled'])
            ->setOrder('created_at', 'DESC');
        return $collection;
    }

    public function getCustomerOrdersWithProduct(): array
    {
        if ($this->ordersWithProduct === null) {
            $orders = $this->getCustomerOrders();
            $ordersWithProduct = [];
            foreach ($orders as $order) {
                $items = $order->getItems();
                foreach ($items as $item) {
                    $itemProductId = $item->getProductId();

                    $parentItemId = $this->configurableTypeResourceModel->getParentIdsByChild($itemProductId);

                    if ($parentItemId) {
                        $itemProductId = $parentItemId[0];
                    }
                    if ($itemProductId == $this->productId) {
                        $ordersWithProduct[] = $order;
                        break;
                    }
                }
            }
            $this->ordersWithProduct = $ordersWithProduct;
        }
        return $this->ordersWithProduct;
    }

    public function getProductOrdersCount(): int
    {
        return sizeof($this->getCustomerOrdersWithProduct());
    }

    public function isCustomerOrderedProduct(): bool
    {
        if ($this->getProductOrdersCount() >= 1) {
            return true;
        }
        return false;
    }

    public function getLastPurchaseDate(): string
    {
        $orders = $this->getCustomerOrdersWithProduct();
        if (empty($orders)) {
            return '';
        }
        $latestOrder = reset($orders);
        return $this->timezone->formatDateTime($latestOrder->getCreatedAt());

    }
}
