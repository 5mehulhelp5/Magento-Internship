<?php
namespace Perspective\WeatherRecommendationWidget\Block\Widget;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Perspective\WeatherRecommendationWidget\Model\Weather\Manager;
use Magento\CatalogWidget\Block\Product\ProductsList;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Rule\Model\Condition\Sql\Builder as SqlBuilder;
use Magento\CatalogWidget\Model\Rule;
use Magento\Widget\Helper\Conditions;
use Magento\Catalog\Block\Product\Context;

class RecommendationWidget extends ProductsList
{
    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;
    /**
     * @var Manager
     */
    protected $recommendationManager;

    protected $recommendationData = null;

    /**
     * @param Context $context
     * @param ProductCollectionFactory $productCollectionFactory
     * @param Visibility $catalogProductVisibility
     * @param HttpContext $httpContext
     * @param SqlBuilder $sqlBuilder
     * @param Rule $rule
     * @param Conditions $conditionsHelper
     * @param Manager $recommendationManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        ProductCollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        HttpContext $httpContext,
        SqlBuilder $sqlBuilder,
        Rule $rule,
        Conditions $conditionsHelper,
        Manager $recommendationManager,
        array $data = []
    ) {
        $this->recommendationManager = $recommendationManager;
        $this->productCollectionFactory = $productCollectionFactory;

        parent::__construct(
            $context,
            $productCollectionFactory,
            $catalogProductVisibility,
            $httpContext,
            $sqlBuilder,
            $rule,
            $conditionsHelper,
            $data
        );
    }

    /**
     * @return array
     */
    public function getRecommendationData(): array
    {
        if ($this->recommendationData !== null) {
            return $this->recommendationData;
        }
        $this->recommendationData = $this->recommendationManager->test();
        return $this->recommendationData;
    }

    /**
     * @return bool
     */
    public function isVisible(): bool
    {
        $data = $this->getRecommendationData();
        return !empty($data['recommended_skus']);
    }

    /**
     * Get collection for widget
     *
     * @return Collection
     */
    public function getBaseCollection(): Collection
    {
        $data = $this->getRecommendationData();
        $skus = $data['recommended_skus'];

        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*')
                    ->addFieldToFilter('sku', ['in' => $skus]);

        return $collection;
    }

    /**
     * Disable widget if not visible
     *
     * @return string
     */
    public function toHtml(): string
    {
        if (!$this->isVisible()) {
            return '';
        }

        return parent::toHtml();
    }

    /**
     * @return string
     */
    public function getTemperature(): string
    {
        $temperature = $this->getRecommendationData()['weather_data']['temperature'];
        return round($temperature, 1);
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->getRecommendationData()['weather_data']['city'];
    }
}
