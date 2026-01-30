<?php

namespace Perspective\PopularProductList\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Perspective\PopularProductList\Service\PopularProducts;

class PopularProductList implements ArgumentInterface
{
    protected $popularProductsService;
    public function __construct(
        PopularProducts $popularProductsService,
    ) {
        $this->popularProductsService = $popularProductsService;
    }

    public function test()
    {
        return $this->popularProductsService->getTopProductStats();
    }

}
