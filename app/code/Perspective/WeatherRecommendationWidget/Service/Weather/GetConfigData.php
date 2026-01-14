<?php
namespace Perspective\WeatherRecommendationWidget\Service\Weather;
use Magento\Framework\App\Config\ScopeConfigInterface;
class GetConfigData
{
    public const XML_PATH_WEATHER = 'weather_widget';
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return array
     */
    public function getWeatherConfig(): array
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WEATHER);
    }

    /**
     * @return string
     */
    public function getWeatherApiKey(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WEATHER . '/general_settings/api_key');
    }

    /**
     * @return string
     */
    public function getGeoApiUrl(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WEATHER . '/general_settings/geo_api_url');
    }

    /**
     * @return string
     */
    public function getWeatherApiUrl(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WEATHER . '/general_settings/weather_api_url');
    }

    /**
     * @return string
     */
    public function getCacheTime(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WEATHER . '/general_settings/data_cache_time');
    }

    /**
     * @return bool
     */
    public function isModuleEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_WEATHER . '/general_settings/enabled');
    }
}
