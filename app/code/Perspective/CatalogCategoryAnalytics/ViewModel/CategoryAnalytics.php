<?php

namespace Perspective\CatalogCategoryAnalytics\ViewModel;

use Magento\Catalog\Model\Category;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Model\Layer\Resolver;
use Perspective\CatalogCategoryAnalytics\Service\CategoryAnalytics as CategoryAnalyticsService;

class CategoryAnalytics implements ArgumentInterface
{
    /**
     * @var Resolver
     */
    protected $layerResolver;
    /**
     * @var CategoryAnalyticsService
     */
    protected $categoryAnalyticsService;

    /**
     * @param Resolver $layerResolver
     * @param CategoryAnalyticsService $categoryAnalyticsService
     */
    public function __construct(
        Resolver          $layerResolver,
        CategoryAnalyticsService $categoryAnalyticsService
    ) {
        $this->layerResolver = $layerResolver;
        $this->categoryAnalyticsService = $categoryAnalyticsService;
    }

    /**
     * @return Category
     */
    protected function getCurrentCategory(): Category
    {
        return $this->layerResolver->get()->getCurrentCategory();
    }

    /**
     * Get data for template
     *
     * @return array
     */
    public function getCategoryAnalyticsData(): array
    {
        $category = $this->getCurrentCategory();
        return $this->categoryAnalyticsService->getAnalytics($category);
    }
}
