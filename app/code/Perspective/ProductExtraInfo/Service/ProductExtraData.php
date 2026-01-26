<?php
namespace Perspective\ProductExtraInfo\Service;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\ConfigurableProduct\Api\LinkManagementInterface;

class ProductExtraData
{
    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;
    /**
     * @var LinkManagementInterface
     */
    protected $configurableChildrenInterface;

    /**
     * @param CategoryRepositoryInterface $categoryRepository
     * @param LinkManagementInterface $configurableChildrenInterface
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        LinkManagementInterface $configurableChildrenInterface
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->configurableChildrenInterface = $configurableChildrenInterface;
    }

    /**
     * @param Product $product
     * @return array
     */
    public function getCategoryNames(Product $product): array
    {
        $categoryNames = [];
        $categoryIds = $product->getCategoryIds();

        foreach ($categoryIds as $categoryId) {
            try {
                $categoryNames[] = $this->categoryRepository->get($categoryId)->getName();
            } catch (NoSuchEntityException $e) {
                continue;
            }
        }
        return array_unique($categoryNames);
    }

    /**
     * Get min price from simple products
     *
     * @param Product $product configurable
     * @return string
     */
    public function getMinSimplePrice(Product $product): string
    {
        $simplePrices = [];
        $simpleProducts = $this->configurableChildrenInterface->getChildren($product->getSku());

        foreach ($simpleProducts as $simpleProduct) {
            $simplePrices[] = $simpleProduct->getPrice();
        }
        return min($simplePrices);
    }
}
