<?php
namespace Perspective\WeatherRecommendationWidget\Model\Weather;
use Perspective\WeatherRecommendationWidget\Api\IpWhoIsApi;
use Perspective\WeatherRecommendationWidget\Api\OpenWeatherMapApi;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;

class WeatherDataCollector
{
    protected $geoApi;
    protected $weatherApi;
    protected $remoteAddress;

    public function __construct(
        IpWhoIsApi $geoApi,
        OpenWeatherMapApi $weatherApi,
        RemoteAddress $remoteAddress
    ) {
        $this->geoApi = $geoApi;
        $this->weatherApi = $weatherApi;
        $this->remoteAddress = $remoteAddress;
    }

    public function collectWeatherData()
    {
        $ip = $this->getUserIp();

        $geoData = $this->geoApi->getGeoData($ip);
        $weatherData = $this->weatherApi->getWeatherData($geoData);
        //exc
        
        $temperature = $weatherData['main']['temp'] - 273.15;
        return [
            'temperature' => $temperature,
            'city' => $geoData['city'],
        ];
    }

    private function getUserIp()
    {
        //return $this->remoteAddress->getRemoteAddress();
        return '8.8.8.8'; // because local magento
    }
}
