<?php
namespace Perspective\WeatherRecommendationWidget\Api;
use Magento\Framework\HTTP\Client\Curl;
use Perspective\WeatherRecommendationWidget\Helper\Config;
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
     * @param array $geoData
     * @return array
     */
    public function getWeatherData($geoData)
    {
        $apiKey = $this->configHelper->getWeatherApi();

        $url = "https://api.openweathermap.org/data/2.5/weather?lat={$geoData['latitude']}&lon={$geoData['longitude']}&appid={$apiKey}";

        $this->curl->get($url);
        return json_decode($this->curl->getBody(), true);
    }
}
