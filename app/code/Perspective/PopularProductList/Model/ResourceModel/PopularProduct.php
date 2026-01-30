<?php
namespace Perspective\PopularProductList\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class PopularProduct extends AbstractDb
{
    protected $_isPkAutoIncrement = false;

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct(): void
    {
        $this->_init('perspective_popular_products', 'product_id');
    }
}
