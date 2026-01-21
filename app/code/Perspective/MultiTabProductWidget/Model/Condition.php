<?php

namespace Perspective\MultiTabProductWidget\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Perspective\MultiTabProductWidget\Model\ResourceModel\Condition as ConditionResourceModel;
use Magento\Rule\Model\Condition\CombineFactory;

class Condition extends AbstractModel
{
    /**
     * @var CombineFactory
     */
    protected $_conditionsFactory;

    /**
     * Condition constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param CombineFactory $conditionsFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CombineFactory $conditionsFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_conditionsFactory = $conditionsFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     */
    protected function _construct(): void
    {
        $this->_init(ConditionResourceModel::class);
    }
}
