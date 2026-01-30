<?php

namespace Perspective\PopularProductList\Model;

use Magento\Framework\Model\AbstractModel;
use Perspective\PopularProductList\Model\ResourceModel\PopularProduct as PopularProductResourceModel;

class PopularProduct extends AbstractModel
{
    /**
     * Initialize resource model
     */
    protected function _construct(): void
    {
        $this->_init(PopularProductResourceModel::class);
    }
}
