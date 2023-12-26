<?php

namespace LR\MenuImage\Setup\Patch\Data;

use Magento\Catalog\Model\Category;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class CategoryAttribute implements DataPatchInterface
{
    /**
     * Undocumented function
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->addAttribute(
            Category::ENTITY,
            'cat_thumbnail',
            [
                'type' => 'varchar',
                'label' => 'Thumbnail',
                'input' => 'image',
                'sort_order' => 100,
                'source' => '',
                'global' => 1,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => null,
                'group' => 'General Information',
                'backend' => \Magento\Catalog\Model\Category\Attribute\Backend\Image::class
            ]
        );
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }
}
