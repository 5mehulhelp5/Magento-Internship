<?php
namespace Perspective\WeatherRecommendationWidget\Api;

use Magento\Framework\HTTP\Client\Curl;
use Perspective\WeatherRecommendationWidget\Exception\ApiException;
use Perspective\WeatherRecommendationWidget\Service\Weather\GetConfigData as WeatherConfigService;
class IpWhoIsApi
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
     * @param $ip
     * @return array
     * @throws ApiException
     */
    public function getGeoData($ip): array
    {
        $apiUrl = $this->weatherConfigService->getGeoApiUrl();
        $fields = 'city,latitude,longitude';

        $url = sprintf('%s%s?fields=%s', $apiUrl, $ip, $fields);
        $this->curl->get($url);

        $geoData = json_decode($this->curl->getBody(), true);
        if (empty($geoData['city'])) {
            throw new ApiException(__('GeoLocation API. Location could not be determined for IP: %1', $ip));
        }

        return $geoData;
    }
}
