<?php
namespace Perspective\WeatherRecommendationWidget\Model\Config\Source;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;

class CategoryList implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @param CollectionFactory $categoryCollectionFactory
     */
    public function __construct(CollectionFactory $categoryCollectionFactory)
    {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * Return array of categories that contain at least one product
     * @return array
     * @throws LocalizedException
     */
    public function toOptionArray()
    {
        $collection = $this->categoryCollectionFactory->create()
            ->addAttributeToSelect('name')
            ->addFieldToFilter('level', ['gt' => 1])
            ->addFieldToFilter('is_active', 1);

        $options = [];
        foreach ($collection as $category) {
            if ($category->getProductCount() > 0) {
                $options[] = [
                    'value' => $category->getId(),
                    'label' => $category->getName()
                ];
            }
        }
        return $options;
    }
}
