<?php

namespace LR\Deposit\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ProductFactory;

class Data extends AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    public const MODULE_ENABLE = 'deposit/configuration/enable';

    /**
     * Undocumented function
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param ProductCollection $productCollection
     * @param ProductFactory $productFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ProductCollection $productCollection,
        ProductFactory $productFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->productCollection = $productCollection;
        $this->productFactory = $productFactory;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getProductCollection()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $quoteItemCollection = $objectManager->create('\Magento\Quote\Model\ResourceModel\Quote\Item\Collection');
        $depositValue = [];
        foreach ($quoteItemCollection as $quoteItem)
        {
            $depositValue[] = $quoteItem->getDeposit();
        }
        $productDepositValue = array_sum($depositValue);

        return $productDepositValue;

        // $productCollection = $this->productCollection;
        // foreach ($productCollection as $productCollectionData) {
        //     $productFactory = $this->productFactory->create();
        //     $product = $productFactory->load($productCollectionData->getId());
        //     $productDeposit[] = $product->getDeposit();
        // }
        // $productDepositValue = array_sum($productDeposit);
        // return $productDepositValue;
    }

    /**
     * Check module enable or diable
     *
     * @return int
     */
    public function isEnable()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::MODULE_ENABLE, $storeScope);
    }
}
