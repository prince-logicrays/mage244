<?php
/**
 * Logicrays
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Logicrays
 * @package     Logicrays_EmailFilter
 * @copyright   Copyright (c) Logicrays (https://www.logicrays.com/)
 */
declare(strict_types=1);

namespace Logicrays\EmailFilter\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Json\Helper\Data as MagentoJsonData;

/**
 * Get Syetem Configuration Data
 */
class Data extends AbstractHelper
{
    public const EMAILFILTER_ENABLE = 'emailfilter/general/enable';
    public const EMAILFILTER_EMAILRESTRICTION = 'emailfilter/general/emaildomainrestrict';
    public const EMAILFILTER_REGISTRATION_RESTRICTION = 'emailfilter/general/registrationrestrict';
    public const EMAILFILTER_CHECKOUT_RESTRICTION = 'emailfilter/general/checkoutrestrict';
    public const EMAILFILTER_CONTACT_RESTRICTION = 'emailfilter/general/contactrestrict';
    public const EMAILFILTER_NEWSLATTER_RESTRICTION = 'emailfilter/general/newslatterrestrict';

    /**
     * @var StoreManagerInterface
     */
    protected $modelStoreManagerInterface;

    /**
     * @var Registry
     */
    protected $frameworkRegistry;

    /**
     * @var MagentoJsonData
     */
    protected $jsonHelper;

    /**
     * Data constructor
     *
     * @param Context $context
     * @param StoreManagerInterface $modelStoreManagerInterface
     * @param Registry $frameworkRegistry
     * @param MagentoJsonData $jsonHelper
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $modelStoreManagerInterface,
        Registry $frameworkRegistry,
        MagentoJsonData $jsonHelper
    ) {
        $this->modelStoreManagerInterface = $modelStoreManagerInterface;
        $this->frameworkRegistry = $frameworkRegistry;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
    }

    /**
     * Get Module Enable or Not
     *
     * @return string|null
     */
    public function isEnabled(): ?string
    {
        return $this->scopeConfig->getValue(
            self::EMAILFILTER_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Email Restriction Enable or Not
     *
     * @return string|null
     */
    public function getEmailrestricton(): ?string
    {
        return $this->scopeConfig->getValue(
            self::EMAILFILTER_EMAILRESTRICTION,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get the value and split them
     *
     * @return void
     */
    public function getEmailrestrictonsplit()
    {
        $value = $this->scopeConfig->getValue(
            self::EMAILFILTER_EMAILRESTRICTION,
            ScopeInterface::SCOPE_STORE
        );
        if (!$value) {
            return "";
        }
        $emailExpression = preg_split('/\r\n|[\r\n]/', $value);
        $encodedData = $this->jsonHelper->jsonEncode($emailExpression);
        return $encodedData;
    }

    /**
     * Get Registration Restriction Enable or Not
     *
     * @return string|null
     */
    public function getRegistrationRestriction(): ?string
    {
        return $this->scopeConfig->getValue(
            self::EMAILFILTER_REGISTRATION_RESTRICTION,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Checkout Restriction Enable or Not
     *
     * @return string|null
     */
    public function getCheckoutRestriction(): ?string
    {
        return $this->scopeConfig->getValue(
            self::EMAILFILTER_CHECKOUT_RESTRICTION,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Contact Restriction Enable or Not
     *
     * @return string|null
     */
    public function getContactRestriction(): ?string
    {
        return $this->scopeConfig->getValue(
            self::EMAILFILTER_CONTACT_RESTRICTION,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Newsletter Restriction Enable or Not
     *
     * @return string|null
     */
    public function getNewslatterRestriction(): ?string
    {
        return $this->scopeConfig->getValue(
            self::EMAILFILTER_NEWSLATTER_RESTRICTION,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check Email Is Allowed or Not
     *
     * @param string $email
     * @return bool
     */
    public function isEmailAllowed($email): ?bool
    {
        $value = $this->scopeConfig->getValue(
            self::EMAILFILTER_EMAILRESTRICTION,
            ScopeInterface::SCOPE_STORE
        );
        if (!$value) {
            return true;
        }
        $emailExpression = preg_split('/\r\n|[\r\n]/', $value);
        foreach ($emailExpression as $emailValue) {
            if (str_contains($email, $emailValue)) {
                return false;
            }
        }
        return true;
    }
}
