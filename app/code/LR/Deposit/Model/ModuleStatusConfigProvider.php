<?php

namespace LR\Deposit\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use LR\Deposit\Helper\Data;

class ModuleStatusConfigProvider implements ConfigProviderInterface
{
    public const XML_MODULE_STATUS_PATH = "deposit/configuration/enable";

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * Undocumented function
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param Data $helper
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Data $helper
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->helper = $helper;
    }

    /**
     * Get config values
     *
     * @return void
     */
    public function getConfig()
    {
        $config = [];
        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $helper = $objectManager->create(\LR\Deposit\Helper\Data::class);

        $moduleIsEnabled = $this->checkIsModuleEnabled(self::XML_MODULE_STATUS_PATH);
        if ($moduleIsEnabled) {
            $config['moduleStatus'] = true;
        } else {
            $config['moduleStatus'] = false;
        }
        if ($this->helper->getProductCollection() != 0) {
            $config['depositValue'] = true;
        } else {
            $config['depositValue'] = false;
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
