<?php

namespace Perspective\ProductExtraInfo\ViewModel;

use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Block\Product\View;
use Perspective\ProductExtraInfo\Service\ProductExtraData;
use Magento\Store\Model\StoreManagerInterface;

class ProductExtraInfo implements ArgumentInterface
{
    protected $currentProduct = null;
    protected $isConfigurable = null;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var View
     */
    protected $productViewBlock;
    /**
     * @var ProductExtraData
     */
    protected $productExtraData;

    /**
     * @param View $productViewBlock
     * @param ProductExtraData $productExtraData
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        View $productViewBlock,
        ProductExtraData $productExtraData,
        StoreManagerInterface $storeManager,
    ) {
        $this->productViewBlock = $productViewBlock;
        $this->productExtraData = $productExtraData;
        $this->storeManager = $storeManager;
    }

    /**
     * @return Product
     */
    protected function getCurrentProduct(): Product
    {
        if ($this->currentProduct === null) {
            $this->currentProduct = $this->productViewBlock->getProduct();
        }
        return $this->currentProduct;
    }

    /**
     * @return array
     */
    public function getCategoryNames(): array
    {
        return $this->productExtraData->getCategoryNames($this->getCurrentProduct());
    }

    /**
     * @return bool
     */
    public function isConfigurable(): bool
    {
        if ($this->isConfigurable === null) {
            $this->isConfigurable = $this->getCurrentProduct()->getTypeId() == 'configurable';
        }
        return $this->isConfigurable;
    }

    /**
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getFormattedMinPrice(): string
    {
        if (!$this->isConfigurable()) {
            return '';
        }

        $minPrice = $this->productExtraData->getMinSimplePrice($this->getCurrentProduct());
        $currency = $this->storeManager->getStore()->getCurrentCurrency()->getCurrencySymbol();

        return sprintf('%s%.2f', $currency, $minPrice);
    }
}
