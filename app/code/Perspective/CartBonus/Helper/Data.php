<?php
namespace Perspective\CartBonus\Helper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\CategoryRepository;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        CategoryRepository $categoryRepository,
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param $value
     * @return array
     */
    public function stringToArray($value)
    {
        return array_map('intval', explode(',', $value));
    }

    /**
     * @param $sku
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCategoryIdsByProductSku($sku): array
    {
        $product = $this->productRepository->get($sku);
        return $product->getCategoryIds();
    }

    /**
     * @param $categoryId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCategoryNameById($categoryId): string
    {
        return $this->categoryRepository->get($categoryId)->getName();
    }

    /**
     * @param $productId
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductById($productId)
    {
        return $this->productRepository->getById($productId);
    }

    /**
     * @param $productId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductSkuById($productId)
    {
        return $this->productRepository->getById($productId)->getSku();
    }
}
