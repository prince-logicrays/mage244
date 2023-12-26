<?php

namespace Cellularisrael\BundleProduct\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table as Table;

class UpgradeData implements UpgradeDataInterface
{
  public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
  {
    $installer = $setup;
    $installer->startSetup();
    if (version_compare($context->getVersion(), '1.0.1', '<')) {

      $installer->getConnection()->addColumn(
          $installer->getTable('catalog_product_bundle_option'),
          'sim_custom_addon',
          [
              'type' => Table::TYPE_INTEGER,
              'nullable' => true,
              'length' => 10,
              'default' => 0,
              'comment' => 'sim_custom_addon'
          ]
      );

      $installer->getConnection()->addColumn(
          $installer->getTable('sales_order_item'),
          'sim_custom_addon',
          [
              'type' => Table::TYPE_TEXT,
              'nullable' => true,
              'length' => 255,
              'comment' => 'Sim Custom Addon'
          ]
      );

      $installer->getConnection()->addColumn(
          $installer->getTable('quote_item'),
          'sim_custom_addon',
          [
              'type' => Table::TYPE_TEXT,
              'nullable' => true,
              'length' => 255,
              'comment' => 'Sim Custom Addon'
          ]
      );
    }

    if (version_compare($context->getVersion(), '1.0.2', '<')) {

    //   $installer->getConnection()->addColumn(
    //       $installer->getTable('cellisrael_mobileplans_orderitems'),
    //       'sim_disable_for_provision',
    //       [
    //           'type' => Table::TYPE_INTEGER,
    //           'nullable' => true,
    //           'length' => 10,
    //           'default' => 0,
    //           'comment' => 'Sim Disable For Provision'
    //       ]
    //   );

    //   $installer->getConnection()->addColumn(
    //       $installer->getTable('cellisrael_mobileplans_orderitems'),
    //       'virtual_flag_not_removeble',
    //       [
    //           'type' => Table::TYPE_INTEGER,
    //           'nullable' => true,
    //           'length' => 10,
    //           'default' => 0,
    //           'comment' => 'Virtual Flag Not Removeble'
    //       ]
    //   );

      $installer->getConnection()->addColumn(
          $installer->getTable('sales_order'),
          'virtual_plan_autofill_phone',
          [
              'type' => Table::TYPE_INTEGER,
              'nullable' => true,
              'length' => 10,
              'default' => 0,
              'comment' => 'Virtual Plan Autofill Phone'
          ]
      );

      $installer->getConnection()->addColumn(
          $installer->getTable('sales_order'),
          'virtual_plan_keyin_phone',
          [
              'type' => Table::TYPE_INTEGER,
              'nullable' => true,
              'length' => 10,
              'default' => 0,
              'comment' => 'Sim Disable For Provision'
          ]
      );

      $installer->getConnection()->addColumn(
          $installer->getTable('catalog_product_bundle_option'),
          'phone_custom_addon',
          [
              'type' => Table::TYPE_INTEGER,
              'nullable' => true,
              'length' => 10,
              'default' => 0,
              'comment' => 'Phone Custom Addon'
          ]
      );

      $installer->getConnection()->addColumn(
          $installer->getTable('sales_order_item'),
          'phone_custom_addon',
          [
              'type' => Table::TYPE_TEXT,
              'nullable' => true,
              'length' => 255,
              'comment' => 'Phone Custom Addon'
          ]
      );

      $installer->getConnection()->addColumn(
          $installer->getTable('quote_item'),
          'phone_custom_addon',
          [
              'type' => Table::TYPE_TEXT,
              'nullable' => true,
              'length' => 255,
              'comment' => 'Phone Custom Addon'
          ]
      );
    }

    if (version_compare($context->getVersion(), '1.0.3', '<')) {
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'linked_virtual_number_plan',
            [
              'type' => Table::TYPE_TEXT,
              'nullable' => true,
              'length' => 255,
              'comment' => 'Linked Virtual Number Plan'
            ]
        );
    }

    if (version_compare($context->getVersion(), '1.0.4', '<')) {
        $installer->getConnection()->dropColumn($installer->getTable('sales_order'), 'linked_virtual_number_plan');
        
        // $installer->getConnection()->addColumn(
        //     $installer->getTable('cellisrael_mobileplans_orderitems'),
        //     'linked_virtual_number_plan',
        //     [
        //       'type' => Table::TYPE_TEXT,
        //       'nullable' => true,
        //       'length' => 255,
        //       'comment' => 'Linked Virtual Number Plan'
        //     ]
        // );
    }



    $installer->endSetup();
  }
}
