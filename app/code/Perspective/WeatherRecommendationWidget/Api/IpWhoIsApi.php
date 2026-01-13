<?php
namespace Perspective\WeatherRecommendationWidget\Api;

use Magento\Framework\HTTP\Client\Curl;
use Perspective\WeatherRecommendationWidget\Exception\ApiException;
class IpWhoIsApi
{
    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @param Curl $curl
     */
    public function __construct(
        Curl $curl
    ) {
        $this->curl = $curl;
    }


    /**
     * @param $ip
     * @return array
     * @throws ApiException
     */
    public function getGeoData($ip): array
    {
        $url = 'https://ipwho.is/' . $ip . '?fields=city,latitude,longitude';

        //$apiUrl = $this->configHelper->getIpWhoIsBaseUrl(); // из конфига
        //$fields = $this->configHelper->getIpWhoIsFields(); // из конфига
        //$url = sprintf('%s%s?fields=%s', $apiUrl, $ip, $fields);


        $this->curl->get($url);

        $geoData = json_decode($this->curl->getBody(), true);
        if (empty($geoData['city'])) {
            throw new ApiException(__('GeoLocation API. Location could not be determined for IP: %1', $ip));
        }

        return $geoData;
    }
}
