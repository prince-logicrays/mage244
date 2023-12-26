<?php

namespace LR\Deposit\Observer;

use Magento\Framework\Event\ObserverInterface;
use LR\Deposit\Helper\Data;

class DepositSaveToQuote implements ObserverInterface
{
    /**
     * Undocumented function
     *
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Undocumented function
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $item = $observer->getEvent()->getData('quote_item');
        // $item->setData('deposit', $this->helper->getProductCollection());

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $checkoutSession = $objectManager->create('\Magento\Checkout\Model\Session')->getQuote();

        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/prince-test.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info(print_r($getDeposite->getData(), true));
    }
}
