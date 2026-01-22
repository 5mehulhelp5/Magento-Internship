<?php
namespace Perspective\MultiTabProductWidget\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Perspective\MultiTabProductWidget\Model\ResourceModel\Condition\CollectionFactory;

class ConditionList implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $collection = $this->collectionFactory->create();
        $options = [];
        foreach ($collection as $item) {
                $options[] = [
                    'label' => $item->getName(),
                    'value' => $item->getConditions(),
                ];
        }
        return $options;
    }
}
