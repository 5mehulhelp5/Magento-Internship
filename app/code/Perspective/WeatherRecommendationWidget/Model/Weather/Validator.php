<?php
namespace Perspective\WeatherRecommendationWidget\Model\Weather;
use Perspective\WeatherRecommendationWidget\Helper\Config;
use Perspective\WeatherRecommendationWidget\Model\Weather\Cookie\Manager as CookieManager;

class Validator
{
    protected $configHelper;
    protected $cookieManager;
    public function __construct(
        Config $configHelper,
        CookieManager $cookieManager
    ) {
        $this->configHelper = $configHelper;
        $this->cookieManager = $cookieManager;
    }

    public function validate(): bool
    {
        return $this->isModuleEnabled() &&
                $this->isModuleConfigured();
    }

    public function isModuleEnabled(): bool //cache
    {
        return $this->configHelper->getConfigValue('weather_widget/general_settings/enabled');
    }

    public function isModuleConfigured(): bool //cache
    {
        $configData = $this->configHelper->getWeatherConfig();

        if (!isset($configData['general_settings'])) {
            return false;
        }

        $general = $configData['general_settings'];
        if (empty($general['api_key'])) {
            return false;
        }

        if (!isset($configData['weather_categories'])) {
            return false;
        }

        $categories = $configData['weather_categories'];
        $requiredKeys = ['category_cold', 'category_cool', 'category_warm', 'category_hot'];

        foreach ($requiredKeys as $key) {
            if (!isset($categories[$key]) || $categories[$key] == '') {
                return false;
            }
        }

        return true;
    }

    public function isCookieSet($cookieName): bool
    {
        if ($this->cookieManager->get($cookieName)) {
            return true;
        }
        return false;
    }


}
