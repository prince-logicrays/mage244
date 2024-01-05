<?php

namespace LR\Deposit\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Quote\Model\QuoteRepository;
use Magento\Quote\Model\QuoteFactory;

class ProductCartUpadte implements ObserverInterface
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param CheckoutSession $checkoutSession
     * @param SerializerInterface $serializer
     * @param ProductRepository $productRepository
     * @param RequestInterface $request
     * @param Data $helper
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        SerializerInterface $serializer,
        RequestInterface $request,
        QuoteRepository $quoteRepository,
        QuoteFactory $quoteFactory
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->serializer = $serializer;
        $this->_request = $request;
        $this->quoteRepository = $quoteRepository;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * Get order delivery estimation
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return string
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $item = $observer->getQuoteItem();
        $itemDeposit = $item->getDeposit();
        $getQty = $item->getQty();
        $quoteId = $item->getQuoteId();
        $quote = $this->quoteRepository->get($quoteId);
        $items = $quote->getItems();
        $depositValues = 0;
        foreach ($items as $item) {
            $deposit = $item->getDeposit();
            $getQty = $item->getQty();
            $totalDeposit = $deposit * $getQty;
            $depositValues += $totalDeposit;
        }
        $quote->setDeposit($depositValues);
        $this->quoteRepository->save($quote);
    }
}
