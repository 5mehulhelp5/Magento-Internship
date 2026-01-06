<?php
namespace Perspective\WeatherRecommendationWidget\Api;
use Magento\Framework\HTTP\Client\Curl;
use Perspective\WeatherRecommendationWidget\Helper\Config;
class OpenWeatherMapApi
{
    protected $curl;
    protected $configHelper;
    public function __construct(
        Curl $curl,
        Config $configHelper
    ) {
        $this->curl = $curl;
        $this->configHelper = $configHelper;
    }

    public function getWeatherData($geoData)
    {
        $apiKey = $this->configHelper->getWeatherApi();

        $url = "https://api.openweathermap.org/data/2.5/weather?lat={$geoData['latitude']}&lon={$geoData['longitude']}&appid={$apiKey}";

        $this->curl->get($url);
        return json_decode($this->curl->getBody(), true); // не сразу ретурн а проверка на наличие полей, и мб эксепшен
    }
}
