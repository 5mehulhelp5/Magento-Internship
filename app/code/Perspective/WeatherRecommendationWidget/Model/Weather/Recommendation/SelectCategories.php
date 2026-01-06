<?php
namespace Perspective\WeatherRecommendationWidget\Model\Weather\Recommendation;

use Perspective\WeatherRecommendationWidget\Helper\Config;
use Perspective\WeatherRecommendationWidget\Helper\Data;

class SelectCategories
{
    protected $configHelper;
    protected $dataHelper;
    public function __construct ( 
        Config $configHelper,
        Data $dataHelper
    ) {
        $this->configHelper = $configHelper;
        $this->dataHelper = $dataHelper;
    }

    public function getScenarioCategories($temperature): array
    {
        $scenario = $this->getScenarioByTemp($temperature);
        $configValue = $this->configHelper->getWeatherConfig()['weather_categories']['category_' . $scenario];
        
        return $this->dataHelper->stringToArray($configValue);
    }

    private function getScenarioByTemp($temperature): string
    {
        if ($temperature <= 5) {
            return 'cold';
        }

        if ($temperature <= 15) {
            return 'cool';
        }

        if ($temperature <= 25) {
            return 'warm';
        }

        return 'hot';
    }
}