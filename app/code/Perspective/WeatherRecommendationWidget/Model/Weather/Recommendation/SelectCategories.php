<?php
namespace Perspective\WeatherRecommendationWidget\Model\Weather\Recommendation;
use Perspective\WeatherRecommendationWidget\Service\Weather\GetConfigData as WeatherConfigService;

class SelectCategories
{
    /**
     * @var WeatherConfigService
     */
    protected $weatherConfigService;
    /**
     * @param WeatherConfigService $weatherConfigService
     */
    public function __construct(
        WeatherConfigService $weatherConfigService
    ) {
        $this->weatherConfigService = $weatherConfigService;
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
        $configValue = $this->weatherConfigService->getWeatherConfig()['weather_categories']['category_' . $scenario];

        return $this->stringToArray($configValue);
    }

    /**
     * Get scenario by temperature
     *
     * @param $temperature
     * @return string
     */
    public function getScenarioByTemp($temperature): string // тип поменять на паблик
    {
        return match(true) {
            $temperature <= 5 => 'cold',
            $temperature <= 15 => 'cool',
            $temperature <= 25 => 'warm',
            default => 'hot',
        };
    }

    /**
     * @param $value
     * @return array
     */
    public function stringToArray($value): array
    {
        return array_map('intval', explode(',', $value));
    }
}
