<?php
namespace Perspective\PopularProductList\Model\ResourceModel\PopularProduct;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Perspective\PopularProductList\Model\ResourceModel\PopularProduct as PopularProductResourceModel;
use Perspective\PopularProductList\Model\PopularProduct as PopularProductModel;

class Collection extends AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(PopularProductModel::class, PopularProductResourceModel::class);
    }
}
