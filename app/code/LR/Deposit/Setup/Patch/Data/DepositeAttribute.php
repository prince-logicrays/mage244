<?php
namespace LR\Deposit\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class DepositeAttribute implements DataPatchInterface
{
   private $_moduleDataSetup;

   private $_eavSetupFactory;

   public function __construct(
       ModuleDataSetupInterface $moduleDataSetup,
       EavSetupFactory $eavSetupFactory
   ) {
       $this->_moduleDataSetup = $moduleDataSetup;
       $this->_eavSetupFactory = $eavSetupFactory;
   }

   public function apply()
   {
       /** @var EavSetup $eavSetup */
       $eavSetup = $this->_eavSetupFactory->create(['setup' => $this->_moduleDataSetup]);

       $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'deposit', [
           'type' => 'int',
           'backend' => '',
           'frontend' => '',
           'label' => 'Deposit',
           'input' => 'text',
           'class' => '',
           'source' => '',
           'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
           'visible' => true,
           'required' => false,
           'user_defined' => false,
           'default' => '',
           'searchable' => false,
           'filterable' => false,
           'comparable' => false,
           'visible_on_front' => false,
           'used_in_product_listing' => true,
           'unique' => false,
       ]);
   }

   public static function getDependencies()
   {
       return [];
   }

   public function getAliases()
   {
       return [];
   }

   public static function getVersion()
   {
      return '1.0.0';
   }
}