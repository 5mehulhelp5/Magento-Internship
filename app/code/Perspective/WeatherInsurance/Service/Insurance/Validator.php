<?php
namespace Perspective\WeatherInsurance\Service\Insurance;

use Perspective\WeatherRecommendationWidget\Service\Weather\Validator as WeatherValidator;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Perspective\WeatherRecommendationWidget\Model\Weather\Recommendation\SelectCategories;
use Perspective\WeatherRecommendationWidget\Model\Weather\Cookie\Manager as WeatherCookieManager;
use Perspective\WeatherRecommendationWidget\Model\Weather\Manager as WeatherManager;
class Validator
{
    /**
     * @var WeatherValidator
     */
    protected $weatherValidator;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var WeatherCookieManager
     */
    protected $cookieManager;
    /**
     * @var SelectCategories
     */
    protected $categorySelector;

    /**
     * @param WeatherValidator $weatherValidator
     * @param ScopeConfigInterface $scopeConfig
     * @param WeatherCookieManager $cookieManager
     * @param SelectCategories $categorySelector
     */
    public function __construct(
        WeatherValidator $weatherValidator,
        ScopeConfigInterface $scopeConfig,
        WeatherCookieManager $cookieManager,
        SelectCategories $categorySelector
    ) {
        $this->weatherValidator = $weatherValidator;
        $this->scopeConfig = $scopeConfig;
        $this->cookieManager = $cookieManager;
        $this->categorySelector = $categorySelector;
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        return $this->weatherValidator->validate() //if weather widget enabled and configured
            && $this->isModuleEnabled()
            && $this->isModuleConfigured()
            && $this->weatherValidator->isCookieSet(WeatherManager::WEATHER_COOKIE_NAME)
            && $this->isCurrentScenarioValid();
    }

    /**
     * @return bool
     */
    public function isModuleEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag('weather_insurance/general_settings/enabled');
    }

    /**
     * check if scenarios selected in config
     *
     * @return bool
     */
    public function isModuleConfigured(): bool
    {
        return !empty($this->getConfigScenarios());
    }

    /**
     * check if current scenario in config scenarios
     *
     * @return bool
     */
    public function isCurrentScenarioValid(): bool //cache
    {
        $temperature = $this->cookieManager->get(WeatherManager::WEATHER_COOKIE_NAME)['temperature'];
        $currentScenario = $this->categorySelector->getScenarioByTemp($temperature);
        $configScenarios = $this->getConfigScenarios();

        return in_array($currentScenario, $configScenarios);
    }

    /**
     * @param $quote
     * @return bool
     */
    public function isInsuranceCheckboxEnabled($quote): bool
    {
        return $quote->getData('delivery_insurance');
    }

    /**
     * @return array
     */
    private function getConfigScenarios(): array
    {
        return $this->stringToArray($this->scopeConfig->getValue('weather_insurance/general_settings/scenario_list'));
    }

    /**
     * @param string $value
     * @return array
     */
    private function stringToArray(string $value): array
    {
        return array_map('trim', explode(',', $value));
    }
}
