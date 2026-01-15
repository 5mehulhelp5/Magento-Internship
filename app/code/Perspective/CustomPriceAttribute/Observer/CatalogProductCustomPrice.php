<?php
namespace Perspective\CustomPriceAttribute\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\RequestInterface;

class CatalogProductCustomPrice implements ObserverInterface
{
    protected RequestInterface $request;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    public function execute(Observer $observer)
    {
        $collection = $observer->getEvent()->getData('collection');
        if ($collection) {
            foreach ($collection as $product) {
                $customPrice = $product->getData('custom_price');
                if ($customPrice != 0) {
                    $product->setPrice($customPrice);
                    $product->setFinalPrice($customPrice);
                }
            }
        }
    }
}
