<?php
namespace Perspective\WeatherRecommendationWidget\Api;
use Magento\Framework\HTTP\Client\Curl;
use Perspective\WeatherRecommendationWidget\Exception\ApiException;
use Perspective\WeatherRecommendationWidget\Service\Weather\GetConfigData as WeatherConfigService;


class OpenWeatherMapApi
{
    /**
     * @var Curl
     */
    protected $curl;
    /**
     * @var WeatherConfigService
     */
    protected $weatherConfigService;

    /**
     * @param Curl $curl
     * @param WeatherConfigService $weatherConfigService
     */
    public function __construct(
        Curl $curl,
        WeatherConfigService $weatherConfigService
    ) {
        $this->curl = $curl;
        $this->weatherConfigService = $weatherConfigService;
    }

    /**
     * @param $geoData
     * @return array
     * @throws ApiException
     */
    public function getWeatherData($geoData): array
    {
        $apiKey = $this->weatherConfigService->getWeatherApiKey();
        $apiUrl = $this->weatherConfigService->getWeatherApiUrl();

        $url = sprintf('%s?lat=%s&lon=%s&appid=%s', $apiUrl, $geoData['latitude'], $geoData['longitude'], $apiKey);
        $this->curl->get($url);

        $weatherData = json_decode($this->curl->getBody(), true);
        if (!isset($weatherData['main']['temp'])) {
            throw new ApiException(__('Weather API. Weather data not found'));
        }

        return $weatherData;
    }
}
