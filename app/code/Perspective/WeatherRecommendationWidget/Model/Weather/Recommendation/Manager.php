<?php
namespace Perspective\WeatherRecommendationWidget\Model\Weather\Recommendation;

use Perspective\WeatherRecommendationWidget\Model\Weather\Recommendation\SelectCategories;
use Perspective\WeatherRecommendationWidget\Model\Weather\Recommendation\SelectProducts;

class Manager
{
    /**
     * @var SelectCategories
     */
    protected $categorySelector;
    /**
     * @var SelectProducts
     */
    protected $productSelector;

    /**
     * @param SelectCategories $categorySelector
     * @param SelectProducts $productSelector
     */
    public function __construct (
        SelectCategories $categorySelector,
        SelectProducts $productSelector
    ) {
        $this->categorySelector = $categorySelector;
        $this->productSelector = $productSelector;
    }

    /**
     * @param $temperature
     * @return array
     */
    public function getRecommendedSkus($temperature)
    {
        $categoryIds = $this->categorySelector->getScenarioCategories($temperature);
        return $this->productSelector->getRandomSalableSkus($categoryIds);
    }

}
