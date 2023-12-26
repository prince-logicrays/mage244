<?php

namespace Cellularisrael\BundleProduct\Model\Bundle\Product;

class OptionList extends \Magento\Bundle\Model\Product\OptionList
{
    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return \Magento\Bundle\Api\Data\OptionInterface[]
     */
    public function getItems(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        $optionCollection = $this->type->getOptionsCollection($product);
        $this->extensionAttributesJoinProcessor->process($optionCollection);
        $optionList = [];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get(\Magento\Framework\App\RequestInterface::class);
        $storeId = $request->getParam('store');
        
        /** @var \Magento\Bundle\Model\Option $option */
        foreach ($optionCollection as $option) {
            $productLinks = $this->linkList->getItems($product, $option->getOptionId());
            /** @var \Magento\Bundle\Api\Data\OptionInterface $optionDataObject */
            $optionDataObject = $this->optionFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $optionDataObject,
                $option->getData(),
                \Magento\Bundle\Api\Data\OptionInterface::class
            );
            $optionDataObject->setOptionId($option->getOptionId())
                ->setTitle($option->getTitle() === null ? $option->getDefaultTitle() : $option->getTitle())
                ->setDefaultTitle($option->getDefaultTitle())
                ->setSku($product->getSku())
                ->setStoreId($storeId)
                ->setFrontendType($option->getFrontendType()) // retrieve "frontend_type" from db
                ->setDescription($option->getDescription()) // retrieve "description" from db
                ->setSimCustomAddon($option->getSimCustomAddon()) // retrieve "description" from db
                ->setPhoneCustomAddon($option->getPhoneCustomAddon()) // retrieve "phone_custom_addon" from db
                ->setMerchant($option->getMerchant()) // retrieve "merchant" from db
                ->setProductLinks($productLinks);
            $optionList[] = $optionDataObject;
        }
        return $optionList;
    }
}