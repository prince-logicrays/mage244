<?php

namespace Cellularisrael\BundleProduct\Plugin;
class OptionPlugin
{
    private $_coreRegistry;

    public function __construct(\Magento\Framework\Registry $registry)
    {
        $this->_coreRegistry = $registry;
    }

     public function aroundrenderPriceString(
        \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option $subject,
        \Closure $proceed,
        $selection,
        $includeContainer = true)
    {   
        //define variable 
        $merchant = 0;
        $amount = 0;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        //load bundle product option object to check merchant 
        $optiondata = $objectManager->create('Magento\Bundle\Model\Option')->load($selection->getOptionId());
        $merchant = $optiondata->getMerchant();

       
        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($selection->getProductId());

        // if bundle product merchant == provider 
        if($merchant == 2 && $product->getIsraelPrice()){
            $amount = (float)$product->getIsraelPrice();
            $priceHtml = "<span class='price-container tax weee'>
                <span data-price-amount='".$amount."' data-price-type='' class='price-wrapper '><span class='price'>".'₪'.$amount."</span></span>
             </span>";   
        }else{
            $myBlock = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Catalog\Block\Product\AbstractProduct');
            $abstractProductBlock = $myBlock->getLayout()->createBlock('\Magento\Catalog\Block\Product\AbstractProduct');
            $priceHtml =  $abstractProductBlock->getProductPrice($product);
        }

        return $priceHtml;
    }
}