<?php
namespace Perspective\CartBonus\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\InventorySalesApi\Api\AreProductsSalableInterface;
class Validation extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $scopeConfig;
    protected $productSalableInterface;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        AreProductsSalableInterface $productSalableInterface
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->productSalableInterface = $productSalableInterface;
    }

    public function isModuleEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag('cartbonus/general_settings/enabled');
    }

    public function isCartRulesApplied($quote): bool
    {
        if ($quote->getBaseSubtotal() == $quote->getBaseSubtotalWithDiscount()) {
            return false;
        }
        return true;
    }

    public function isBonusEnabled($bonus_code): bool
    {
        return $this->scopeConfig->isSetFlag('cartbonus/' . $bonus_code . '/enabled');
    }

    public function getBonusConfig($bonus_code) // перенос в дата хелпер
    {
        return $this->scopeConfig->getValue('cartbonus/' . $bonus_code);
    }

    public function isProductSalable($sku)
    {
        return $this->productSalableInterface->execute([$sku], 1);
    }
}
