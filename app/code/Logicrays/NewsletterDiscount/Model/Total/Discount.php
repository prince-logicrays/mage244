<?php

namespace Logicrays\NewsletterDiscount\Model\Total;

use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class Discount extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    public const XML_MODULE_STATUS = "newsletter_discount/discount/enable";

    public const XML_MODULE_DISCOUNT_PRICE = "newsletter_discount/discount/discount_price";

    /**
     * @var QuoteValidator
     */
    protected $quoteValidator = null;

    /**
     * @param \Magento\Quote\Model\QuoteValidator $quoteValidator
     * @param SubscriberFactory $subscriberFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param CollectionFactory $orderCollectionFactory
     */
    public function __construct(
        \Magento\Quote\Model\QuoteValidator $quoteValidator,
        SubscriberFactory $subscriberFactory,
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $orderCollectionFactory
    ) {
        $this->quoteValidator = $quoteValidator;
        $this->subscriberFactory = $subscriberFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * Set Custom Discount
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return void
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        $moduleIsEnabled = $this->checkIsModuleEnabled(self::XML_MODULE_STATUS);
        if ($moduleIsEnabled == 1) {
            $getDiscountPrice = $this->getDiscountPrice(self::XML_MODULE_DISCOUNT_PRICE);
            $customerId = $quote->getCustomerId();
            if (!$customerId) {
                return;
            }
            $startDate = date("Y-m-d h:i:s", strtotime('2024-01-02'));
            $orderData = $this->orderCollectionFactory->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'customer_id',
                $customerId
            )->addAttributeToFilter('created_at', ['from'=>$startDate]);
            $orderCount = count($orderData);
            $subscriber = $this->subscriberFactory->create()->loadByCustomerId($customerId);
            if ($subscriber->isSubscribed() && $orderCount == 0) {
                $total->setCustomDiscount($getDiscountPrice);
                $total->setBaseCustomDiscount($getDiscountPrice);
                $total->setGrandTotal($total->getGrandTotal() - $getDiscountPrice);
                $total->setBaseGrandTotal($total->getBaseGrandTotal() - $getDiscountPrice);
                return $this;
            }
        }
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
     * Check module enabled
     *
     * @param string $path
     * @return void
     */
    public function getDiscountPrice($path)
    {
        return $this->_scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Set amount
     *
     * @param Address\Total $total
     * @return void
     */
    protected function clearValues(Address\Total $total)
    {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param Address\Total $total
     * @return array|null
     */
    /**
     * Assign subtotal amount and label to address object
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param Address\Total $total
     * @return array
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $moduleIsEnabled = $this->checkIsModuleEnabled(self::XML_MODULE_STATUS);
        if ($moduleIsEnabled == 1) {
            return [
                'code' => 'custom_discount',
                'title' => 'Discount',
                'value' => $this->getDiscountPrice(self::XML_MODULE_DISCOUNT_PRICE)
            ];
        }
    }

    /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Discount');
    }
}
