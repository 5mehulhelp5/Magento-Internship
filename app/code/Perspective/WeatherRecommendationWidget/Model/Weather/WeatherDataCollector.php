<?php
namespace Perspective\WeatherRecommendationWidget\Model\Weather;
use Perspective\WeatherRecommendationWidget\Api\IpWhoIsApi;
use Perspective\WeatherRecommendationWidget\Api\OpenWeatherMapApi;

class WeatherDataCollector
{
    protected $geoApi;
    protected $weatherApi;

    public function __construct(
        IpWhoIsApi $geoApi,
        OpenWeatherMapApi $weatherApi
    ) {
        $this->geoApi = $geoApi;
        $this->weatherApi = $weatherApi;
    }

    public function collectWeatherData()
    {
        $ip = 1; //di customerdata

        $geoData = $this->geoApi->getGeoData($ip);
        $weatherData = $this->weatherApi->getWeatherData($geoData);

        $temperature = $weatherData['main']['temp'] - 273.15;
        return [
            'temperature' => $temperature,
            'city' => $geoData['city'],
        ];

    }
}
