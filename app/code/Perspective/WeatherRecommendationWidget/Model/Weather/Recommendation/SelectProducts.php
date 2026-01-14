<?php
namespace Perspective\WeatherRecommendationWidget\Model\Weather\Recommendation;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\InventorySalesApi\Api\AreProductsSalableInterface;

class SelectProducts
{
    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;
    /**
     * @var AreProductsSalableInterface
     */
    protected $salableInterface;

    /**
     * @param CollectionFactory $productCollectionFactory
     * @param AreProductsSalableInterface $salableInterface
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        AreProductsSalableInterface $salableInterface
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->salableInterface = $salableInterface;
    }

    /**
     * Get random products sku by categories with filters
     *
     * @param array $categoryIds
     * @return array
     */
    public function getRandomSalableSkus (array $categoryIds): array
    {
        $collection = $this->productCollectionFactory->create();
        //filters(status, visibility, categories)
        $collection ->setStoreId(0)
                    ->addAttributeToFilter('status', 1)
                    ->addAttributeToFilter('visibility', ['in' => [2,3,4]])
                    ->addCategoriesFilter(['in' => $categoryIds]);

        $skus = $collection->getColumnValues('sku');
        $selected = [];
        $limit = 4;
        $stockId = 1;

        //filter(salable) and get 4 random sku
        shuffle($skus);
        foreach (array_chunk($skus, 20) as $chunk) {
            $results = $this->salableInterface->execute($chunk, $stockId);

            foreach ($results as $result) {
                if ($result->isSalable()) {
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
