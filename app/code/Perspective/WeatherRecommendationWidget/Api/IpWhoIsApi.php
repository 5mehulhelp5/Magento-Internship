<?php
namespace Perspective\WeatherRecommendationWidget\Api;

use Magento\Framework\HTTP\Client\Curl;
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
     * @param string $ip
     * @return array
     */
    public function getGeoData($ip)
    {
        $url = 'https://ipwho.is/' . $ip . '?fields=city,latitude,longitude';
        $this->curl->get($url);
        return json_decode($this->curl->getBody(), true);
    }
}
