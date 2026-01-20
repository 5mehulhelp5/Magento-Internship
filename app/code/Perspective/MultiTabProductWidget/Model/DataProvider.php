<?php

namespace Perspective\MultiTabProductWidget\Model;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Perspective\MultiTabProductWidget\Model\ResourceModel\Condition\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var array
     */
    protected $loadedData;

    // @codingStandardsIgnoreStart
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $conditionCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $conditionCollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
    // @codingStandardsIgnoreEnd

    /**
     * @return array
     */
    public function getData()
    {

        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();

        foreach ($items as $condition) {
            $this->loadedData[$condition->getConsultationId()] = $condition->getData();
        }
        return $this->loadedData;
    }
}
