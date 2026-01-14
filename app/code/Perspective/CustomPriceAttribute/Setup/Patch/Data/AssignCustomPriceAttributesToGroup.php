<?php
namespace Perspective\CustomPriceAttribute\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\Product;

class AssignCustomPriceAttributesToGroup implements DataPatchInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;
    private EavSetupFactory $eavSetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        // Получаем Default Attribute Set ID
        $attributeSetId = $eavSetup->getDefaultAttributeSetId(Product::ENTITY);

        // Получаем ID группы General
        $groupId = $eavSetup->getAttributeGroupId(Product::ENTITY, $attributeSetId, 'General');

        // Привязываем атрибуты к группе General
        $eavSetup->addAttributeToGroup(Product::ENTITY, $attributeSetId, $groupId, 'custom_price', 10);
        $eavSetup->addAttributeToGroup(Product::ENTITY, $attributeSetId, $groupId, 'custom_price_allow_modify', 20);

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public static function getDependencies(): array
    {
        return [
            AddCustomPriceAttribute::class
        ];
    }

    public function getAliases(): array
    {
        return [];
    }
}
