<?php
namespace Perspective\MultiTabProductWidget\Model\ResourceModel\Condition;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Perspective\MultiTabProductWidget\Model\ResourceModel\Condition as ConditionResourceModel;
use Perspective\MultiTabProductWidget\Model\Condition as ConditionModel;

class Collection extends AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(ConditionModel::class, ConditionResourceModel::class);
    }
}
