<?php

namespace Logicrays\NewsletterDiscount\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\Session;
use Magento\Newsletter\Model\SubscriberFactory;

class ConfigProvider implements ConfigProviderInterface
{
    public const XML_MODULE_STATUS = "newsletter_discount/discount/enable";

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $session
     * @param SubscriberFactory $subscriberFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Session $session,
        SubscriberFactory $subscriberFactory
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->session = $session;
        $this->subscriberFactory = $subscriberFactory;
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
        if ($moduleIsEnabled && $subscriber->isSubscribed()) {
            $config['moduleStatus'] = true;
            $config['customDeposit'] = true;
        } else {
            $config['moduleStatus'] = false;
            $config['customDeposit'] = false;
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
}
