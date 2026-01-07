<?php
namespace Perspective\WeatherRecommendationWidget\Model\Weather\Recommendation;

use Perspective\WeatherRecommendationWidget\Helper\Config;
use Perspective\WeatherRecommendationWidget\Helper\Data;

class SelectCategories
{
    /**
     * @var Config 
     */
    protected $configHelper;
    /**
     * @var Data 
     */
    protected $dataHelper;

    /**
     * @param Config $configHelper
     * @param Data $dataHelper
     */
    public function __construct ( 
        Config $configHelper,
        Data $dataHelper
    ) {
        $this->configHelper = $configHelper;
        $this->dataHelper = $dataHelper;
    }

    /**
     * Get categories by scenario
     *
     * @param $temperature
     *
     * @return array
     */
    public function getScenarioCategories($temperature): array
    {
        $scenario = $this->getScenarioByTemp($temperature);
        $configValue = $this->configHelper->getWeatherConfig()['weather_categories']['category_' . $scenario];
        
        return $this->dataHelper->stringToArray($configValue);
    }

    /**
     * Get scenario by temperature
     *
     * @param $temperature
     * @return string
     */
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