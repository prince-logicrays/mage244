<?php

namespace LR\Deposit\Model\Total\Quote;

use LR\Deposit\Helper\Data;

class Deposit extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * Collect grand total address amount
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     */
    /**
     * @var [type]
     */
    protected $quoteValidator = null;

    /**
     * Undocumented function
     *
     * @param \Magento\Quote\Model\QuoteValidator $quoteValidator
     * @param Data $helper
     */
    public function __construct(
        \Magento\Quote\Model\QuoteValidator $quoteValidator,
        Data $helper
    ) {
        $this->quoteValidator = $quoteValidator;
        $this->helper = $helper;
        // echo "<pre>";
        // print_r($this->helper->getProductCollection());exit;
    }

    /**
     * Undocumented function
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

        $moduleEnable = $this->helper->isEnable();
        if ($moduleEnable == 1) {
            $balance = $quote->getDeposit();
            $total->setTotalAmount('deposit', $balance);
            $total->setBaseTotalAmount('deposit', $balance);
            $total->setDeposit($balance);
            $total->setBaseDeposit($balance);

            return $this;
        }
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
        return [
            'code' => 'deposit',
            'title' => 'Deposit',
            'value' => $quote->getDeposit()
        ];
    }

    /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Deposit');
    }
}
