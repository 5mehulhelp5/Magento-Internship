<?php
namespace Perspective\CartBonus\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\InventorySalesApi\Api\AreProductsSalableInterface;
class Validation extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var AreProductsSalableInterface
     */
    protected $productSalableInterface;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param AreProductsSalableInterface $productSalableInterface
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        AreProductsSalableInterface $productSalableInterface
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->productSalableInterface = $productSalableInterface;
    }

    /**
     * @return bool
     */
    public function isModuleEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag('cartbonus/general_settings/enabled');
    }

    /**
     * @param $quote
     * @return bool
     */
    public function isCartRulesApplied($quote): bool
    {
        if ($quote->getBaseSubtotal() == $quote->getBaseSubtotalWithDiscount()) {
            return false;
        }
        return true;
    }

    /**
     * @param $bonus_code
     * @return bool
     */
    public function isBonusEnabled($bonus_code): bool
    {
        return $this->scopeConfig->isSetFlag('cartbonus/' . $bonus_code . '/enabled');
    }

    /**
     * @param $bonus_code
     * @return mixed
     */
    public function getBonusConfig($bonus_code) // перенос в дата хелпер
    {
        return $this->scopeConfig->getValue('cartbonus/' . $bonus_code);
    }

    /**
     * @param $sku
     * @return \Magento\InventorySalesApi\Api\Data\IsProductSalableResultInterface[]
     */
    public function isProductSalable($sku)
    {
        return $this->productSalableInterface->execute([$sku], 1);
    }
}
