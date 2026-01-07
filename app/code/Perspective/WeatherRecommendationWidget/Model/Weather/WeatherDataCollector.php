<?php
namespace Perspective\WeatherRecommendationWidget\Model\Weather;

use Perspective\WeatherRecommendationWidget\Api\IpWhoIsApi;
use Perspective\WeatherRecommendationWidget\Api\OpenWeatherMapApi;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;

class WeatherDataCollector
{
    /**
     * @var IpWhoIsApi 
     */
    protected $geoApi;
    /**
     * @var OpenWeatherMapApi 
     */
    protected $weatherApi;
    /**
     * @var RemoteAddress 
     */
    protected $remoteAddress;

    /**
     * @param IpWhoIsApi $geoApi
     * @param OpenWeatherMapApi $weatherApi
     * @param RemoteAddress $remoteAddress
     */
    public function __construct(
        IpWhoIsApi $geoApi,
        OpenWeatherMapApi $weatherApi,
        RemoteAddress $remoteAddress
    ) {
        $this->geoApi = $geoApi;
        $this->weatherApi = $weatherApi;
        $this->remoteAddress = $remoteAddress;
    }

    /**
     * @return array
     */
    public function collectWeatherData()
    {
        $ip = $this->getUserIp();

        $geoData = $this->geoApi->getGeoData($ip);
        if (empty($geoData['city'])) {
            throw new \RuntimeException('Location could not be determined for IP: ' . $ip);
        }

        $weatherData = $this->weatherApi->getWeatherData($geoData);
        if (!isset($weatherData['main']['temp'])) {
            throw new \RuntimeException('Weather data not found');
        }
        $temperature = $weatherData['main']['temp'] - 273.15;

        return [
            'temperature' => $temperature,
            'city' => $geoData['city'],
        ];
    }

    /**
     * @return string
     */
    private function getUserIp()
    {
        //return $this->remoteAddress->getRemoteAddress();
        return '143.106.0.0'; // because local magento

        //  143.106.0.0     - 26
        //  52.255.111.63   - 14
    }
}
