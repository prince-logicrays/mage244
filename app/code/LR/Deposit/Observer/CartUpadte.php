<?php

namespace LR\Deposit\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\RequestInterface;
use Magento\Quote\Model\QuoteRepository;
use Magento\Quote\Model\QuoteFactory;

class CartUpadte implements ObserverInterface
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
        ProductRepository $productRepository,
        RequestInterface $request,
        QuoteRepository $quoteRepository,
        QuoteFactory $quoteFactory
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->serializer = $serializer;
        $this->productRepository = $productRepository;
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
        $items = $observer->getEvent()->getCart()->getQuote()->getItems();
        $quote = $this->checkoutSession->getQuote();
        $item = $quote->getItemById($itemId);
        $product = $this->productRepository->getById($item->getProductId());
        $currentProductDeposit = $product->getDeposit();

        $getDeposit = $quote->getDeposit();
        $quoteId = $quote->getId();
        $quoteData = $this->quoteFactory->create()->load($quoteId);
        $oldDeposit = $quoteData->getDeposit();
        $allDeposit = $oldDeposit - $currentProductDeposit;

        $quote = $this->quoteRepository->get($quoteId);
        $quote->setDeposit($allDeposit);
        $this->quoteRepository->save($quote);
    }
}
