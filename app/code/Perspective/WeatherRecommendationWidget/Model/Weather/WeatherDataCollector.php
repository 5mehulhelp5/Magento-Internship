<?php
namespace Perspective\WeatherRecommendationWidget\Model\Weather;

use Perspective\WeatherRecommendationWidget\Api\IpWhoIsApi;
use Perspective\WeatherRecommendationWidget\Api\OpenWeatherMapApi;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Perspective\WeatherRecommendationWidget\Exception\ApiException;

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
     * @throws ApiException
     */
    public function collectWeatherData(): array
    {
        $ip = $this->getUserIp();
        $geoData = $this->geoApi->getGeoData($ip);
        $weatherData = $this->weatherApi->getWeatherData($geoData);
        $temperature = $weatherData['main']['temp'] - 273.15;

        return [
            'temperature' => $temperature,
            'city' => $geoData['city'],
        ];
    }

    /**
     * @return string
     */
    private function getUserIp(): string
    {
        //return $this->remoteAddress->getRemoteAddress();
        return '143.106.0.0'; // because local magento

        //  143.106.0.0     - 26
        //  52.255.111.63   - 14
    }
}
