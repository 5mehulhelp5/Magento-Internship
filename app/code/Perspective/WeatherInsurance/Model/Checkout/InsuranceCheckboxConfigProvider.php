<?php
namespace Perspective\WeatherInsurance\Model\Checkout;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Checkout\Model\Session as CheckoutSession;
use Perspective\WeatherInsurance\Service\Insurance\Validator as InsuranceValidationService;
use Magento\Framework\App\Config\ScopeConfigInterface;

class InsuranceCheckboxConfigProvider implements ConfigProviderInterface
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;
    /**
     * @var InsuranceValidationService
     */
    protected $insuranceValidationService;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param CheckoutSession $checkoutSession
     * @param InsuranceValidationService $insuranceValidationService
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        InsuranceValidationService $insuranceValidationService,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->insuranceValidationService = $insuranceValidationService;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getConfig(): array
    {
        $quote = $this->checkoutSession->getQuote();

        return [
            'weatherInsurance' => [
                'isCheckboxVisible' => $this->insuranceValidationService->validate(),
                'isDefaultChecked' => (bool) $quote->getData('delivery_insurance'),
                'insurancePrice' => $this->scopeConfig->getValue('weather_insurance/general_settings/insurance_price'),
                'checkboxLabel' => __($this->scopeConfig->getValue('weather_insurance/general_settings/checkbox_label')),
                'checkboxDescription' => __($this->scopeConfig->getValue('weather_insurance/general_settings/checkbox_description'))
            ]
        ];
    }
}
