<?php

namespace LR\CustomPayment\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\SamplePaymentGateway\Gateway\Http\Client\ClientMock;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;

class CustomConfigProvider implements ConfigProviderInterface
{
    public const CUSTOM_PAYMENT_TITLE = 'payment/custompayment/custom_payment_title';
    public const LOGO_DIR = 'payments/logo/';
    public const CUSTOM_PAYMENT_LOGO = 'payment/custompayment/logo';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Undocumented function
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getConfig()
    {
        $config = [];
        $config['payment']['customTitle'] = $this->getCustomPaymentTitle();
        $config['payment']['logo'] = $this->getLogo();
        return $config;
    }

    /**
     * Get custom payment title
     *
     * @return int
     */
    public function getCustomPaymentTitle()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::CUSTOM_PAYMENT_TITLE, $storeScope);
    }

    public function getCustomPaymentLogo()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::CUSTOM_PAYMENT_LOGO, $storeScope);
    }

    public function getLogo()
    {
        $logoUrl = false;
        if ($file = trim((string)$this->getCustomPaymentLogo())) {
            $fileUrl = self::LOGO_DIR . $file;
            $mediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
            $logoUrl = $mediaUrl . $fileUrl;
        }
        return $logoUrl;
    }
}
