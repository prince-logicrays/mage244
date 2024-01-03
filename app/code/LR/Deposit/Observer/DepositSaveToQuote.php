<?php

namespace LR\Deposit\Observer;

use Magento\Framework\Event\ObserverInterface;
use LR\Deposit\Helper\Data;
use Magento\Quote\Model\QuoteFactory;

class DepositSaveToQuote implements ObserverInterface
{
    /**
     * Undocumented function
     *
     * @param Data $helper
     */
    public function __construct(
        Data $helper,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        QuoteFactory $quoteFactory
    ) {
        $this->helper = $helper;
        $this->quoteRepository = $quoteRepository;
        $this->quoteFactory = $quoteFactory;
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
        $getDeposit = $item->getProduct()->getDeposit();
        $item->setDeposit($getDeposit);
        $item->save();
        $quoteId = $item->getQuoteId();
        $quoteData = $this->quoteFactory->create()->load($quoteId);
        $oldDeposit = $quoteData->getDeposit();
        $allDeposit = $oldDeposit + $getDeposit;
        $quote = $this->quoteRepository->get($quoteId);
        $quote->setDeposit($allDeposit);
        $this->quoteRepository->save($quote);
    }
}
