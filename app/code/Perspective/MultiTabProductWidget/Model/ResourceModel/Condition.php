<?php

namespace Perspective\MultiTabProductWidget\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Condition extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct(): void
    {
        $this->_init('perspective_widget_conditions', 'condition_id');
    }
}
