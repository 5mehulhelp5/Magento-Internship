<?php
namespace Perspective\CustomPriceAttribute\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class CatalogProductCustomPrice implements ObserverInterface
{
    /**
     * Replaces the product price in the catalog with the custom_price value
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $collection = $observer->getEvent()->getData('collection');
        if ($collection) {
            foreach ($collection as $product) {
                $customPrice = $product->getData('custom_price');
                if ($customPrice != 0) {
                    $product->setPrice($customPrice);
                }
            }
        }
    }
}
