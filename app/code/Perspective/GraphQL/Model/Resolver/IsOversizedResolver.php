<?php
namespace Perspective\GraphQL\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class IsOversizedResolver implements ResolverInterface
{
    public function resolve(
        Field $field,
              $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ): array
    {
        $product = $value['model'];

        return [
            [
                'store' => $context->getExtensionAttributes()->getStore()->getName(),
                'overweighted' => $product->getWeight() >= 50,
                'current_customer_id' => $context->getUserId(),
                'user_type' => $context->getUserType(),
            ]
        ];
    }
}
