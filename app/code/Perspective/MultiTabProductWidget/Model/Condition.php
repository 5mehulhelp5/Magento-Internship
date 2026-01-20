<?php

namespace Perspective\MultiTabProductWidget\Model;

use Magento\Framework\Model\AbstractModel;
use Perspective\MultiTabProductWidget\Model\ResourceModel\Condition as ConditionResourceModel;

class Condition extends AbstractModel
{
    /**
     * Initialize model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(ConditionResourceModel::class);
    }
}
