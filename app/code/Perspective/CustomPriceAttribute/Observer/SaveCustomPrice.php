<?php
namespace Perspective\CustomPriceAttribute\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\RequestInterface;

class SaveCustomPrice implements ObserverInterface
{
    protected RequestInterface $request;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    public function execute(Observer $observer)
    {
        /*$product = $observer->getEvent()->getDataObject();
        $post = $this->request->getPostValue('product');

        if (!$post) return;

        if (isset($post['custom_price'])) {
            $product->setCustomPrice($post['custom_price']);
        }*/

        //if (isset($post['custom_price_allow_modify'])) {
        //    $product->setData('custom_price_allow_modify', $post['custom_price_allow_modify']);
        //}
    }
}
