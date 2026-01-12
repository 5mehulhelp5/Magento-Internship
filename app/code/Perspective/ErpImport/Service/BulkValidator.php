<?php
namespace Perspective\ErpImport\Service;

use Magento\Catalog\Api\ProductRepositoryInterface;

class BulkValidator
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        productRepositoryInterface $productRepository,
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * Validate product row
     *
     * @param array $row
     * @return array
     */
    public function rowIsValid(array $row): array
    {
        try {
            $product = $this->productRepository->get($row['sku']);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return ['valid' => false, 'message' => 'Product not found'];
        }

        if (!is_numeric($row['price'])) {
            return ['valid' => false, 'message' => 'Invalid price'];
        }

        if ($row['status'] != 0 && $row['status'] != 1) {
            return ['valid' => false, 'message' => 'Invalid status'];
        }

        return ['valid' => true, 'message' => ''];
    }
}
