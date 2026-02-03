<?php
namespace Perspective\PopularProductList\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Perspective\PopularProductList\Service\PopularProductsManagement;
use Magento\Framework\DataObject\IdentityInterface;
use Perspective\PopularProductList\Service\ConfigData;

class PopularProductList implements ArgumentInterface, IdentityInterface
{
    /**
     * @var PopularProductsManagement
     */
    protected $popularProductsManager;
    /**
     * @var ConfigData
     */
    protected $configDataService;

    /**
     * @param PopularProductsManagement $popularProductsManager
     * @param ConfigData $configDataService
     */
    public function __construct(
        PopularProductsManagement $popularProductsManager,
        ConfigData $configDataService
    ) {
        $this->popularProductsManager = $popularProductsManager;
        $this->configDataService = $configDataService;
    }

    /**
     * Get data for template
     *
     * @return array
     */
    public function getItems(): array
    {

        if (!$this->configDataService->isModuleEnabled()) {
            return [];
        }
      return $this->popularProductsManager->getTopProducts();
    }

    /**
     * Set tag for page cache
     *
     * @return string[]
     */
    public function getIdentities(): array
    {
        return ['perspective_product_top_list'];
    }
}
