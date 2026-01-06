<?php
namespace Perspective\WeatherRecommendationWidget\Model\Weather\Recommendation;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\InventorySalesApi\Api\AreProductsSalableInterface;

class SelectProducts
{
    protected $productCollectionFactory;
    protected $stockHelper;
    protected $salableInterface;

    public function __construct(
        CollectionFactory $productCollectionFactory,
        AreProductsSalableInterface $salableInterface
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->salableInterface = $salableInterface;
    }

    public function getRandomSalableSkus (array $categoryIds)
    {
        $collection = $this->productCollectionFactory->create();
        $collection ->setStoreId(0)
                    ->addAttributeToFilter('status', 1)
                    ->addAttributeToFilter('visibility', ['in' => [2,3,4]])
                    ->addCategoriesFilter(['in' => $categoryIds]);

        $skus = $collection->getColumnValues('sku');
        $selected = [];
        $limit = 4;
        $stockId = 1;

        shuffle($skus);
        foreach (array_chunk($skus, 20) as $chunk) {
            $results = $this->salableInterface->execute($chunk, $stockId);

            foreach ($results as $result) {
                if ($result->isSalable() == true) {
                    $selected[] = $result->getSku();

                    if (count($selected) >= $limit) {
                        break 2;
                    }
                }
            }
        }
        return $selected;
    }
}