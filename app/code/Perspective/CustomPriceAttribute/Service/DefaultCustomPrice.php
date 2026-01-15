<?php

namespace Perspective\CustomPriceAttribute\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
class DefaultCustomPrice
{
    protected $scopeConfig;
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getDefaultCustomPrice($object): float
    {
        $price = $object->getData('price');
        $priceIncreasePercentage = (int)$this->scopeConfig->getValue('custom_price_attribute/general_settings/price_increase_percentage');
        return (float)$price * (1 + $priceIncreasePercentage / 100);
    }

    public function isDefaultCustomPrice($object, $price): bool
    {
        $roundedPrice = round($price, 2, PHP_ROUND_HALF_UP);
        $roundedDefaultPrice = round($this->getDefaultCustomPrice($object), 2, PHP_ROUND_HALF_UP);
        return $roundedDefaultPrice === $roundedPrice;
    }
}
