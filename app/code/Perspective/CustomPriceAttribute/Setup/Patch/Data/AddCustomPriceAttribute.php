<?php

namespace Perspective\CustomPriceAttribute\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Perspective\CustomPriceAttribute\Model\Attribute\Backend\CustomPrice;

class AddCustomPriceAttribute implements DataPatchInterface
{
    protected ModuleDataSetupInterface $moduleDataSetup;
    protected EavSetupFactory $eavSetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @inheritDoc
     */
    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        // Атрибут custom_price
        $eavSetup->addAttribute(
            Product::ENTITY,
            'custom_price',
            [
                'type' => 'decimal',
                'backend' => CustomPrice::class,
                'frontend' => '',
                'label' => 'Custom Price',
                'input' => 'price',
                'required' => false,
                'user_defined' => true,
                'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                'visible' => true,
                'used_in_product_listing' => true,
            ]
        );

        // Атрибут custom_price_allow_modify
        $eavSetup->addAttribute(
            Product::ENTITY,
            'custom_price_allow_modify',
            [
                'type' => 'int',
                'label' => 'Allow Modify Custom Price',
                'input' => 'boolean',
                'required' => false,
                'user_defined' => true,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'default' => 0,
                'visible' => true,
                'used_in_product_listing' => true,
            ]
        );

        $this->moduleDataSetup->getConnection()->endSetup();
    }







    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
       return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }


}
