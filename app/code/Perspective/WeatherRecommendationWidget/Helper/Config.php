<?php
namespace Perspective\WeatherRecommendationWidget\Helper;
use Magento\Store\Model\ScopeInterface;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    public const XML_PATH_WEATHER = 'weather_widget';

    /**
     * @return array
     */
    public function getWeatherConfig()
    {
        return $this->getConfigValue(self::XML_PATH_WEATHER);
    }

    public function getConfigValue($key)
    {
        return $this->scopeConfig->getValue($key);
    }

    /**
     * @return string
     */
    public function getWeatherApi()
    {
        return $this->getConfigValue(self::XML_PATH_WEATHER . '/general_settings/api_key');
    }

    /**
     * @return string
     */
    public function getCacheTime()
    {
        return $this->getConfigValue(self::XML_PATH_WEATHER . '/general_settings/data_cache_time');
    }



}
