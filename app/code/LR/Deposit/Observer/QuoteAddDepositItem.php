<?php

namespace LR\Deposit\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\RequestInterface;
use Magento\Quote\Model\QuoteRepository;

class QuoteAddDepositItem implements ObserverInterface
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
        QuoteRepository $quoteRepository
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->serializer = $serializer;
        $this->productRepository = $productRepository;
        $this->_request = $request;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Get order delivery estimation
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return string
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $this->_request->getParam('product');
        if ($product) {
            if ($this->_request->getFullActionName() == 'checkout_cart_add') {
                $items = $observer->getEvent()->getData('items');

                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $quoteCollection = $objectManager->create('\Magento\Quote\Model\QuoteFactory');
                $quote = $quoteCollection->create()->load(170);

                $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/prince-test.log');
                $logger = new \Zend_Log();
                $logger->addWriter($writer);
                $logger->info(print_r($observer->getEvent()->getData(),true));

                foreach ($items as $item) {
                    $itemDeposite = [];
                    $quote = $this->quoteRepository->get($item->getQuoteId());
                    $itemDeposite[] = $item->getProduct()->getDeposit();
                    $totalDepositValue = array_sum($itemDeposite);
                    $quote->setData('deposit', $totalDepositValue);
                    $this->quoteRepository->save($quote);
                }
            }
        }
    }
}
