<?php

namespace Perspective\PopularProductList\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Perspective\PopularProductList\Service\PopularProductsManagement;
use Magento\Framework\DataObject\IdentityInterface;


class PopularProductList implements ArgumentInterface, IdentityInterface
{
    protected $popularProductsManager;
    public function __construct(
        PopularProductsManagement $popularProductsManager,
    ) {
        $this->popularProductsManager = $popularProductsManager;
    }

    public function test()
    {
        return $this->popularProductsManager->getTopProducts();
    }

    public function getIdentities()
    {
        return ['perspective_product_top_list'];
    }

}
