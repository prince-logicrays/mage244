<?php
namespace Cellularisrael\BundleProduct\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\DB\Ddl\Table as Table;

class InstallData implements InstallDataInterface
{
    
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $installer->getConnection()->addColumn(
            $installer->getTable('catalog_product_bundle_option'),
            'frontend_type',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'length' => 255,
                'default' => 'option',
                'comment' => 'Frontend Type'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('catalog_product_bundle_option'),
            'description',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'length' => '2M',
                'comment' => 'Description'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('catalog_product_bundle_option'),
            'merchant',
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'length' => 10,
                'default' => 1,
                'comment' => 'Merchant'
            ]
        );

        $installer->endSetup();
    }
}
