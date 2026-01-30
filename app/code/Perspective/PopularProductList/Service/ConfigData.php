<?php
namespace Perspective\PopularProductList\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;

class ConfigData
{
    private const XML_PATH_ENABLED = 'perspective_popular_products/general_settings/enabled';
    private const XML_PATH_UPDATE_FREQUENCY = 'perspective_popular_products/general_settings/update_frequency';
    private const XML_PATH_DISPLAY_COUNT = 'perspective_popular_products/general_settings/display_count';

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
     * @return bool
     */
    public function isModuleEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag($this::XML_PATH_ENABLED);
    }

    /**
     * @return int
     */
    public function getUpdateFrequency(): int
    {
        return (int)$this->scopeConfig->getValue($this::XML_PATH_UPDATE_FREQUENCY);
    }

    /**
     * @return int
     */
    public function getDisplayCount(): int
    {
        return (int)$this->scopeConfig->getValue($this::XML_PATH_DISPLAY_COUNT);
    }
}
