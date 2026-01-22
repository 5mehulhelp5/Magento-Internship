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

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $conditionCollectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $conditionCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $conditionCollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

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
            $this->loadedData[$condition->getConditionId()] = $condition->getData();
        }
        return $this->loadedData;
    }
}
