<?php
namespace Perspective\WeatherRecommendationWidget\Helper;
use Magento\Store\Model\ScopeInterface;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    public const XML_PATH_WEATHER = 'weather_widget';

    public function getWeatherConfig()
    {
        return $this->getConfigValue(self::XML_PATH_WEATHER);
    }
    public function getConfigValue($key)
    {
        return $this->scopeConfig->getValue($key);
    }

    public function getWeatherApi()
    {
        return $this->getConfigValue(self::XML_PATH_WEATHER . '/general_settings/api_key');
    }

    public function getCacheTime()
    {
        return $this->getConfigValue(self::XML_PATH_WEATHER . '/general_settings/data_cache_time');
    }



}
