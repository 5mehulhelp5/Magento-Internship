<?php
namespace Perspective\CustomPriceAttribute\Ui\DataProvider\Product\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Form\Field;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Ui\Component\Form;
use Perspective\CustomPriceAttribute\Service\DefaultCustomPrice as DefaultCustomPriceService;

class CustomPrice extends AbstractModifier
{
    const FIELD_CUSTOM_PRICE = 'custom_price';
    const FIELD_USE_CONFIG = 'use_config_custom_price';
    const DATA_SCOPE_PRODUCT = 'data.product';
    const CONTAINER_PREFIX = 'container_';

    /**
     * @var ArrayManager
     */
    protected $arrayManager;
    /**
     * @var LocatorInterface
     */
    protected $locator;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var DefaultCustomPriceService
     */
    protected $defaultCustomPriceService;

    /**
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     * @param ScopeConfigInterface $scopeConfig
     * @param DefaultCustomPriceService $defaultCustomPriceService
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        ScopeConfigInterface $scopeConfig,
        DefaultCustomPriceService $defaultCustomPriceService
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->scopeConfig = $scopeConfig;
        $this->defaultCustomPriceService = $defaultCustomPriceService;
    }

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data): array
    {
        return $data;
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta): array
    {
        return $this->customizeCustomPriceFields($meta);
    }

    /**
     * Configure custom price field and its controlling "Use Config" select.
     *
     * @param array $meta
     * @return array
     */
    protected function customizeCustomPriceFields(array $meta): array
    {
        // Find the path to the custom price field
        $pricePath = $this->arrayManager->findPath(self::FIELD_CUSTOM_PRICE, $meta, null, 'children');
        if (!$pricePath) {
            return $meta;
        }

        // Find the container for the custom price field
        $containerPath = $this->arrayManager->findPath(self::CONTAINER_PREFIX . self::FIELD_CUSTOM_PRICE, $meta, null, 'children');

        // Add imports(bind select field), styling and validation to custom price field
        $meta = $this->arrayManager->merge(
            $pricePath . '/arguments/data/config',
            $meta,
            [
                'imports' => [
                    'disabled' => '!${$.provider}:' . self::DATA_SCOPE_PRODUCT . '.' . self::FIELD_USE_CONFIG . ':value',
                    '__disableTmpl' => ['disabled' => false],
                ],
                'additionalClasses' => 'admin__field-small',
                'validation' => [
                    'validate-zero-or-greater' => true
                ],
            ]
        );

        if ($containerPath) {
            // Configure the container
            $meta = $this->arrayManager->merge(
                $containerPath . '/arguments/data/config',
                $meta,
                [
                    'component' => 'Magento_Ui/js/form/components/group',
                    'label' => false,
                    'required' => false
                ]
            );
        }

        if ($containerPath) {
            $product = $this->locator->getProduct();

            // Add the "Use Config" select field controlling custom price
            $meta = $this->arrayManager->set(
                $containerPath . '/children/' . self::FIELD_USE_CONFIG . '/arguments/data/config',
                $meta,
                [
                    'dataType' => 'boolean',
                    'formElement' => Form\Element\Select::NAME,
                    'componentType' => Field::NAME,
                    //'component' => 'Magento_Ui/js/form/element/single-checkbox',
                    //'prefer' => 'checkbox',

                    'dataScope' => 'use_config_custom_price',

                    'options' => [
                        ['label' => __('Allow Modify'), 'value' => 1],
                        ['label' => __('Not Allow Modify'), 'value' => 0],
                    ],
                    'valueMap' => [
                        'true' => '1',
                        'false' => '0',
                    ],
                    'label' => __('Allow Modify'),
                    'description' => __('Allow Modify'),
                    'sortOrder' => 10,
                    'value' => (int)!$this->defaultCustomPriceService->isDefaultCustomPrice($product)
                ]
            );
        }
        return $meta;
    }
}

