<?php

namespace Perspective\WeatherInsurance\Model\Totals;

use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Framework\Phrase;
use Perspective\WeatherInsurance\Service\Insurance\Validator as InsuranceValidationService;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Insurance extends AbstractTotal
{
    public const INSURANCE_TOTAL_TITLE = 'Insurance Total';
    public const INSURANCE_TOTAL_CODE = 'perspective_delivery_insurance_total';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var InsuranceValidationService
     */
    protected $insuranceValidationService;

    /**
     * @param InsuranceValidationService $insuranceValidationService
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        InsuranceValidationService $insuranceValidationService,
        ScopeConfigInterface $scopeConfig,
    ) {
        $this->insuranceValidationService = $insuranceValidationService;
        $this->scopeConfig = $scopeConfig;
        $this->setCode('bonus_total');
    }

    /**
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return $this
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ): Insurance {
        parent::collect($quote, $shippingAssignment, $total);

        $address = $shippingAssignment->getShipping()->getAddress();
        $items = $this->_getAddressItems($address);
        if (!count($items)) {
            return $this;
        }

        //в валидацию значение параметра из таблицы

        $insurancePrice = $this->getInsurancePrice();
        $total->addTotalAmount(self::INSURANCE_TOTAL_CODE, $insurancePrice);
        $total->addBaseTotalAmount(self::INSURANCE_TOTAL_CODE, $insurancePrice);

        return $this;
    }

    /**
     * @param Total $total
     */
    protected function clearValues(Total $total): void
    {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
    }

    /**
     * @param Quote $quote
     * @param Total $total
     * @return array
     */
    public function fetch(Quote $quote, Total $total): array
    {
        return [
            'code' => $this->getCode(),
            'title' => $this->getLabel(),
            'value' => $this->getInsurancePrice(),
        ];
    }

    /**
     * @return Phrase
     */
    public function getLabel(): Phrase
    {
        return __(self::INSURANCE_TOTAL_TITLE);
    }

    /**
     * @return string
     */
    private function getInsurancePrice(): string
    {
        $price = '0';
        if ($this->insuranceValidationService->validate()) {
            $price = $this->scopeConfig->getValue('weather_insurance/general_settings/insurance_price');
        }
        return $price;
    }
}
