<?php
namespace Cellularisrael\BundleProduct\Block;

class AdvanceBundle extends \Magento\Framework\View\Element\Template
{
	protected $_registry;
	protected $_productCollectionFactory;
	protected $_productFactory; 
	protected $_currency; 

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Directory\Model\Currency $currency,
		array $data = []
	) {
		$this->_registry = $registry;
		$this->_productCollectionFactory = $productCollectionFactory;
		$this->_productFactory = $productFactory;
		$this->_currency = $currency;
		parent::__construct($context);
	}

	public function getProduct()
	{
		return $this->_registry->registry('current_product');		
	}

	public function getPlanProductProviderForList($product)
	{
		$provider = 0;

		if($product->getTypeId() == 'bundle'){

			$selectionCollection = $product->getTypeInstance(true)
	            ->getSelectionsCollection($product->getTypeInstance(true)->getOptionsIds($product), $product);

         	$itemIds = array();

	        foreach ($selectionCollection as $item) {
	            $itemIds[] = $item->getId();	            
	        }
	        
	        $collection = $this->_productCollectionFactory->create()
        				->addAttributeToSelect('*')
        				->addAttributeToFilter('entity_id', ['in' => $itemIds])
        				->addAttributeToFilter('is_plan', 1);
        				
			
			if($collection->getSize()){
				$productId = $collection->getFirstItem()->getId();
				foreach ($selectionCollection as $option) {
					if($option->getEntityId() == $productId){
						$providerid = $option->getOptionId();
					}
				}
				$_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
				$optioncollection = $_objectManager->get('Magento\Bundle\Model\Option')->load($providerid);
				$provider = $optioncollection->getData('merchant');
			}
		}

		return $provider;
	}	



	public function getPlanProductProvider()
	{
		$product = $this->getProduct();
		$provider = 0;

		if($product->getTypeId() == 'bundle'){

			$selectionCollection = $product->getTypeInstance(true)
	            ->getSelectionsCollection($product->getTypeInstance(true)->getOptionsIds($product), $product);

         	$itemIds = array();

	        foreach ($selectionCollection as $item) {
	            $itemIds[] = $item->getId();	            
	        }
	        
	        $collection = $this->_productCollectionFactory->create()
        				->addAttributeToSelect('*')
        				->addAttributeToFilter('entity_id', ['in' => $itemIds])
        				->addAttributeToFilter('is_plan', 1);
        				
			
			if($collection->getSize()){
				$productId = $collection->getFirstItem()->getId();
				foreach ($selectionCollection as $option) {
					if($option->getEntityId() == $productId){
						$providerid = $option->getOptionId();
					}
				}
				$_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
				$optioncollection = $_objectManager->get('Magento\Bundle\Model\Option')->load($providerid);
				$provider = $optioncollection->getData('merchant');
			}
		}

		return $provider;
	}

	public function getPlanProduct()
	{
		$product = $this->getProduct();

		if($product->getTypeId() == 'bundle'){

			$selectionCollection = $product->getTypeInstance(true)
	            ->getSelectionsCollection($product->getTypeInstance(true)->getOptionsIds($product), $product);

         	$itemIds = array();

	        foreach ($selectionCollection as $item) {
	            $itemIds[] = $item->getId();	            
	        }
	        
	        $collection = $this->_productCollectionFactory->create()
        				->addAttributeToSelect('*')
        				->addAttributeToFilter('entity_id', ['in' => $itemIds])
        				->addAttributeToFilter('is_plan', 1);
        				
			
			if($collection->getSize()){
				$productId = $collection->getFirstItem()->getId();
				return $this->_productFactory->create()->load($productId);
			}
		}

		return $product;
	}

	public function getIsaraelCurrencySymbol()
	{
		$currency = $this->_currency->load('ILS'); 
		return $currency->getCurrencySymbol();
	}

	public function convertCurrency($from,$to,$amount)
	{
	    if($from == $to)
	        return $amount;
	    $this->_currency->load($from);
	    $rate = $this->_currency->getAnyRate($to);
	    return number_format($rate*$amount,2);

	}

}
