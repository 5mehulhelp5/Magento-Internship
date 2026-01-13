<?php
namespace Perspective\WeatherRecommendationWidget\Model\Weather;
use Perspective\WeatherRecommendationWidget\Helper\Config;
use Perspective\WeatherRecommendationWidget\Model\Weather\Cookie\Manager as CookieManager;

class Validator
{
    /**
     * @var Config
     */
    protected $configHelper;
    /**
     * @var CookieManager
     */
    protected $cookieManager;

    /**
     * @param Config $configHelper
     * @param CookieManager $cookieManager
     */
    public function __construct(
        Config $configHelper,
        CookieManager $cookieManager
    ) {
        $this->configHelper = $configHelper;
        $this->cookieManager = $cookieManager;
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        return $this->isModuleEnabled() &&
                $this->isModuleConfigured();
    }

    /**
     * @return bool
     */
    public function isModuleEnabled(): bool
    {
        return $this->configHelper->getConfigValue('weather_widget/general_settings/enabled');
    }

    /**
     * @return bool
     */
    public function isModuleConfigured(): bool
    {
        $result = true;
        $configData = $this->configHelper->getWeatherConfig();

        if (!isset($configData['general_settings'])) {
            $result = false;
        }

        $general = $configData['general_settings'];
        if (empty($general['api_key'])) {
            $result = false;
        }

        if (!isset($configData['weather_categories'])) {
            $result = false;
        }

        $categories = $configData['weather_categories'];
        $requiredKeys = ['category_cold', 'category_cool', 'category_warm', 'category_hot'];

        foreach ($requiredKeys as $key) {
            if (!isset($categories[$key]) || $categories[$key] == '') {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * @param string $cookieName
     * @return bool
     */
    public function isCookieSet(string $cookieName): bool
    {
        if ($this->cookieManager->get($cookieName)) {
            return true;
        }
        return false;
    }


}
