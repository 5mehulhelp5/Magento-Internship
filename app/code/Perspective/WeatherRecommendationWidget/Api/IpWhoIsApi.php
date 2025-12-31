<?php
namespace Perspective\WeatherRecommendationWidget\Api;
use Magento\Framework\HTTP\Client\Curl;
class IpWhoIsApi
{
    protected $curl;
    public function __construct(
        Curl $curl
    ) {
        $this->curl = $curl;
    }

    public function getGeoData($ip)
    {
        $url = 'https://ipwho.is/' . $ip . '?fields=city,latitude,longitude';
        $this->curl->get($url);
        return json_decode($this->curl->getBody(), true);

        // не сразу вернуть а проверить что есть и если нет нужных полей то throw $e
    }
}
