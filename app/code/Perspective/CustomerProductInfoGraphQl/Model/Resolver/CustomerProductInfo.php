<?php
namespace Perspective\CustomerProductInfoGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Perspective\CustomerProductInfoGraphQl\Service\CurrentCustomer;
use Perspective\CustomerProductInfoGraphQl\Service\CustomerProductOrders;

class CustomerProductInfo implements ResolverInterface
{
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomerService;
    /**
     * @var CustomerProductOrders
     */
    protected $customerProductOrdersService;

    /**
     * @param CurrentCustomer $currentCustomerService
     * @param CustomerProductOrders $customerProductOrdersService
     */
    public function __construct(
        CurrentCustomer $currentCustomerService,
        CustomerProductOrders $customerProductOrdersService
    ) {
        $this->currentCustomerService = $currentCustomerService;
        $this->customerProductOrdersService = $customerProductOrdersService;
    }

    /**
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     */
    public function resolve(
        Field $field,
              $context, // userId and userType
        ResolveInfo $info,
        array $value = null,
        array $args = null // product id
    ): array {
        return $this->collectData($info, $context, $args);
    }

    /**
     * Prepare data for graphql response
     *
     * @param ResolveInfo $info
     * @param $context
     * @param array $args
     * @return array
     */
    private function collectData(ResolveInfo $info, $context, array $args): array
    {
        //set data in services
        $this->currentCustomerService->setCustomerId($context->getUserId());
        $this->customerProductOrdersService->setProductId($args['productId']);

        // get field selection map in graphql request
        $fieldSelection = $info->getFieldSelection();

        // default response data
        $isCustomerLoggedIn = $this->currentCustomerService->isCustomerLoggedIn();
        $data = [
            'customerIsLoggedIn' => $isCustomerLoggedIn,
        ];

        // not logged in data
        if (!$isCustomerLoggedIn) {
            return array_merge($data, [
                'hasPurchased'     => false,
                'lastPurchaseDate' => '',
                'ordersCount'      => 0,
                'customerGroup'    => 'Guest',
                'customText'       => ''
            ]);
        }

        // set field in data if selected in graphql request
        if (!empty($fieldSelection['customText'])) {
            $data['customText'] = __('customText');
        }

        if (!empty($fieldSelection['customerGroup'])) {
            $data['customerGroup'] = $this->currentCustomerService->getCustomerGroupName();
        }

        if (!empty($fieldSelection['hasPurchased'])) {
            $data['hasPurchased'] = $this->customerProductOrdersService->isCustomerOrderedProduct();
        }

        if (!empty($fieldSelection['lastPurchaseDate'])) {
            $data['lastPurchaseDate'] = $this->customerProductOrdersService->getLastPurchaseDate();
        }

        if (!empty($fieldSelection['ordersCount'])) {
            $data['ordersCount'] = $this->customerProductOrdersService->getProductOrdersCount();
        }
        return $data;
    }
}
