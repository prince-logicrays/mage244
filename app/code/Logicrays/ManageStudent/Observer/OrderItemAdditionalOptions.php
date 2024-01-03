<?php

namespace Logicrays\ManageStudent\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * OrderItemAdditionalOptions
 */
class OrderItemAdditionalOptions implements ObserverInterface
{
    private $quoteItems = [];
    private $quote = null;
    private $order = null;
    
    /**
     * __construct
     *
     * @param \Magento\Store\Model\StoreManagerInterface storeManager
     * @param \Magento\Framework\View\LayoutInterface layout
     * @param \Magento\Framework\App\RequestInterface request
     * @param \Magento\Framework\Serialize\SerializerInterface serializer
     *
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        $this->_layout = $layout;
        $this->_storeManager = $storeManager;
        $this->_request = $request;
        $this->serializer = $serializer;
    }
    
    /**
     * execute
     *
     * @param EventObserver observer
     *
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        $this->quote = $observer->getQuote();
        $this->order = $observer->getOrder();
        foreach ($this->order->getItems() as $orderItem) {
            if ($quoteItem = $this->getQuoteItemById($orderItem->getQuoteItemId())) {
                if ($additionalOptionsQuote = $quoteItem->getOptionByCode('additional_options')) {
                    if ($additionalOptionsOrder = $orderItem->getProductOptionByCode('additional_options')) {
                        $additionalOptions = array_merge($additionalOptionsQuote, $additionalOptionsOrder);
                    } else {
                        $additionalOptions = $additionalOptionsQuote;
                    }
                    if (count($additionalOptions->getData()) > 0) {
                        $options = $orderItem->getProductOptions();
                        $options['additional_options'] = $this->serializer->unserialize($additionalOptions->getValue());
                        $orderItem->setProductOptions($options);
                    }
                }
            }
        }
    }
    
    /**
     * getQuoteItemById
     *
     * @param mixed id
     *
     * @return void
     */
    private function getQuoteItemById($id)
    {
        if (empty($this->quoteItems)) {
            if ($this->quote->getItems()) {
                foreach ($this->quote->getItems() as $item) {
                    $this->quoteItems[$item->getId()] = $item;
                }
            }
        }
        if (array_key_exists($id, $this->quoteItems)) {
            return $this->quoteItems[$id];
        }

        return null;
    }
}