<?php
namespace Perspective\WeatherRecommendationWidget\Model\Weather\Recommendation;

use Perspective\WeatherRecommendationWidget\Model\Weather\Recommendation\SelectCategories;
use Perspective\WeatherRecommendationWidget\Model\Weather\Recommendation\SelectProducts;

class Manager
{
    protected $categorySelector;
    protected $productSelector;
    public function __construct ( 
        SelectCategories $categorySelector,
        SelectProducts $productSelector
    ) {
        $this->categorySelector = $categorySelector;
        $this->productSelector = $productSelector;
    }

    public function getRecommendedSkus($temperature) //rename
    {
        $categoryIds = $this->categorySelector->getScenarioCategories($temperature);
        return $this->productSelector->getRandomSalableSkus($categoryIds);
    }

}