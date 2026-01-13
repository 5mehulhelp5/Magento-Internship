<?php
namespace Perspective\WeatherRecommendationWidget\Api;
use Magento\Framework\HTTP\Client\Curl;
use Perspective\WeatherRecommendationWidget\Helper\Config;
use Perspective\WeatherRecommendationWidget\Exception\ApiException;

class OpenWeatherMapApi
{
    /**
     * @var Curl
     */
    protected $curl;
    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * @param Curl $curl
     * @param Config $configHelper
     */
    public function __construct(
        Curl $curl,
        Config $configHelper
    ) {
        $this->curl = $curl;
        $this->configHelper = $configHelper;
    }

    /**
     * @param $geoData
     * @return array
     * @throws ApiException
     */
    public function getWeatherData($geoData): array
    {
        $apiKey = $this->configHelper->getWeatherApi();
        $url = "https://api.openweathermap.org/data/2.5/weather?lat={$geoData['latitude']}&lon={$geoData['longitude']}&appid={$apiKey}";
        $this->curl->get($url);

        $weatherData = json_decode($this->curl->getBody(), true);
        if (!isset($weatherData['main']['temp'])) {
            throw new ApiException(__('Weather API. Weather data not found'));
        }

        return $weatherData;
    }
}
