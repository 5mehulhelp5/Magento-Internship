<?php
namespace Perspective\CustomerProductInfoGraphQl\Model\Resolver;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
class CustomerProductInfo implements ResolverInterface
{
    protected $context = [];
    protected $productId = null;
    protected $ordersWithProduct = null;

    protected $customerRepository;
    protected $groupRepository;
    protected $collectionFactory;
    protected $timezone;
    protected $configurableTypeResourceModel;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        GroupRepositoryInterface $groupRepository,
        CollectionFactory $collectionFactory,
        TimezoneInterface $timezone,
        Configurable $configurableTypeResourceModel
    ) {
        $this->customerRepository = $customerRepository;
        $this->groupRepository = $groupRepository;
        $this->collectionFactory = $collectionFactory;
        $this->timezone = $timezone;
        $this->configurableTypeResourceModel = $configurableTypeResourceModel;
    }

    public function resolve(
        Field $field,
              $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ): array {

        //$args - product id
        //$context - userId and userType

        $this->context = $context;
        $this->productId = $args['productId'];

        if (!$this->isUserLoggedIn()) {
            return [
                'customerIsLoggedIn' => false,
                'hasPurchased' => false,
                'lastPurchaseDate' => '',
                'ordersCount' => 0,
                'customerGroup' => 'Not logged in',
                'customText' => ''
            ];
        }

        return [
            'customerIsLoggedIn' => true,
            'hasPurchased' => $this->isCustomerOrderedProduct(),
            'lastPurchaseDate' => $this->getLastPurchaseDate(),
            'ordersCount' => $this->getProductOrdersCount(),
            'customerGroup' => $this->getGroupName(),
            'customText' => 'customText'
        ];
    }



    public function isUserLoggedIn(): bool
    {
        $userId = $this->context->getUserId();

        if (!$userId) {
            return false;
        }
        return true;
    }

    public function getCustomer(): CustomerInterface
    {
        $userId = $this->context->getUserId();
        return $this->customerRepository->getById($userId);
    }


    public function getGroupName(): string //rename
    {
        return $this->groupRepository->getById($this->getCustomer()->getGroupId())->getCode();
    }

    public function getCustomerOrders(): Collection
    {
        $collection = $this->collectionFactory->create()
            ->addFieldToSelect(['entity_id', 'status', 'created_at'])
            ->addFieldToFilter('customer_id', $this->context->getUserId())
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
