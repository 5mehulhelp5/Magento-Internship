<?php
namespace Perspective\WeatherRecommendationWidget\Service\Weather;
use Perspective\WeatherRecommendationWidget\Model\Weather\Cookie\Manager as CookieManager;
use Perspective\WeatherRecommendationWidget\Service\Weather\GetConfigData as WeatherConfigService;
class Validator
{
    /**
     * @var CookieManager
     */
    protected $cookieManager;
    /**
     * @var GetConfigData
     */
    protected $weatherConfigService;

    /**
     * @param CookieManager $cookieManager
     * @param GetConfigData $weatherConfigService
     */
    public function __construct(
        CookieManager $cookieManager,
        WeatherConfigService $weatherConfigService
    ) {
        $this->cookieManager = $cookieManager;
        $this->weatherConfigService = $weatherConfigService;
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        return $this->weatherConfigService->isModuleEnabled() &&
            $this->isModuleConfigured();
    }

    /**
     * @return bool
     */
    public function isModuleConfigured(): bool
    {
        $configData = $this->weatherConfigService->getWeatherConfig();

        if (
            !isset($configData['general_settings']) ||
            empty($configData['general_settings']['weather_api_key']) ||
            empty($configData['general_settings']['weather_api_url']) ||
            empty($configData['general_settings']['geo_api_url']) ||
            !isset($configData['weather_categories'])
        ) {
            return false;
        }

        $categories = $configData['weather_categories'];
        $requiredKeys = ['category_cold', 'category_cool', 'category_warm', 'category_hot'];
        foreach ($requiredKeys as $key) {
            if (empty($categories[$key])) {
                return false;
            }
        }
        return true;
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
