<?php

namespace Logicrays\NewsletterDiscount\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\Session;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class ConfigProvider implements ConfigProviderInterface
{
    public const XML_MODULE_STATUS = "newsletter_discount/discount/enable";

    public const XML_MODULE_DISCOUNT_NAME = "newsletter_discount/discount/discount_name";

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $session
     * @param SubscriberFactory $subscriberFactory
     * @param CollectionFactory $orderCollectionFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Session $session,
        SubscriberFactory $subscriberFactory,
        CollectionFactory $orderCollectionFactory
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->session = $session;
        $this->subscriberFactory = $subscriberFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * Get config values
     *
     * @return void
     */
    public function getConfig()
    {
        $config = [];
        $moduleIsEnabled = $this->checkIsModuleEnabled(self::XML_MODULE_STATUS);
        $customerId = $this->session->getCustomer()->getId();
        $subscriber = $this->subscriberFactory->create()->loadByCustomerId($customerId);
        $startDate = date("Y-m-d h:i:s", strtotime('2024-01-02'));
        $orderData = $this->orderCollectionFactory->create()->addFieldToSelect(
            '*'
        )->addFieldToFilter(
            'customer_id',
            $customerId
        )->addAttributeToFilter('created_at', ['from'=>$startDate]);
        $orderCount = count($orderData);
        if ($moduleIsEnabled && $subscriber->isSubscribed() && $orderCount == 0) {
            $config['moduleStatus'] = true;
            $config['customDeposit'] = true;
            $config['getDiscountName'] = $this->getDiscountName(self::XML_MODULE_DISCOUNT_NAME);
        } else {
            $config['moduleStatus'] = false;
            $config['customDeposit'] = false;
            $config['getDiscountName'] = false;
        }
        return $config;
    }

    /**
     * Check module enabled
     *
     * @param string $path
     * @return void
     */
    public function checkIsModuleEnabled($path)
    {
        return $this->_scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get discount name
     *
     * @param string $path
     * @return void
     */
    public function getDiscountName($path)
    {
        return $this->_scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
