<?php
namespace Perspective\CustomPriceAttribute\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
class DefaultCustomPrice
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Calculates the default custom price based on the product's base price
     *  and the price increase percentage from configuration
     *
     * @param $object
     * @return float
     */
    public function getDefaultCustomPrice($object): float
    {
        $price = $object->getData('price');
        $priceIncreasePercentage = (int)$this->scopeConfig->getValue('custom_price_attribute/general_settings/price_increase_percentage');
        return (float)$price * (1 + $priceIncreasePercentage / 100);
    }

    /**
     * Determines if the product's custom price is the default calculated price
     *  or a manually set value
     *
     * @param $object
     * @return bool  true if custom_price is null or matches the calculated default price
     */
    public function isDefaultCustomPrice($object): bool
    {
        $price = $object->getData('custom_price');
        if ($price === null) {
            return true;
        }

        $roundedPrice = round($price, 2, PHP_ROUND_HALF_UP);
        $roundedDefaultPrice = round($this->getDefaultCustomPrice($object), 2, PHP_ROUND_HALF_UP);
        return $roundedDefaultPrice === $roundedPrice;
    }
}
