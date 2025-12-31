<?php
namespace Perspective\WeatherRecommendationWidget\Helper;
use Magento\Store\Model\ScopeInterface;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function getWeatherConfig()
    {
        return $this->getConfigValue('weather_widget');
    }
    public function getConfigValue($key)
    {
        return $this->scopeConfig->getValue($key);
    }

    public function getWeatherApi()
    {
        return $this->getConfigValue('weather_widget/general_settings/api_key');
    }




}
