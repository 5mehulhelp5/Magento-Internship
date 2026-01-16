<?php
namespace Perspective\CustomPriceAttribute\Model\Attribute\Backend;

use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Perspective\CustomPriceAttribute\Service\DefaultCustomPrice as DefaultCustomPriceService;

/**
 * Backend model for the custom_price attribute.
 *
 * Handles saving logic for custom_price:
 * - If "Not Allow Modify" is selected in the admin form, the price is calculated using a formula.
 * - If "Allow Modify" is selected, the entered value is saved as is.
 * - If entered custom price is 0, the value is not saved.
 */
class CustomPrice extends AbstractBackend
{
    /**
     * @var DefaultCustomPriceService
     */
    protected $defaultCustomPriceService;

    /**
     * @param DefaultCustomPriceService $defaultCustomPriceService
     */
    public function __construct(
        DefaultCustomPriceService $defaultCustomPriceService,
    )
    {
        $this->defaultCustomPriceService = $defaultCustomPriceService;
    }

    /**
     * @param $object
     * @return void
     */
    public function beforeSave($object): void
    {
        $allowModify = $object->getData('use_config_custom_price');

        // If "Not Allow Modify" is selected in admin product form, calculate the default custom price
        if ($allowModify !== null && (int)$allowModify === 0) {
            $defaultCustomPrice = $this->defaultCustomPriceService->getDefaultCustomPrice($object);

            // If the calculated price is 0, do not save the attribute
            if ($defaultCustomPrice == 0) {
                $object->unsetData('custom_price');
            } else {
                $object->setData('custom_price', $defaultCustomPrice);
            }
        }
    }
}
