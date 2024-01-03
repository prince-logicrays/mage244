<?php

namespace Logicrays\ManageStudent\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote\Item;
use Magento\Checkout\Model\Cart\Interceptor;

class CheckoutCartUpdateItemsAfterObserver implements ObserverInterface
{

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        die();
        /**
         * @var Interceptor $cart
         * @var Item[] $items
         *
         */
        $cart = $observer->getData('cart');
        $info = $observer->getData('info');
        $items = $cart->getQuote()->getItems();
        foreach ($items as $item) {
            echo "<pre>";
            print_r($item);
            exit();

            if (!isset($info[$item->getId()]['your_custom_label'])) {
                continue;
            }

            $additionalOptions = [];
            $additionalOptions[] = [
                'label' => 'your_custom_label',
                'value' => $info[$item->getId()]['your_custom_label']
            ];

            $item->addOption([
                'code' => 'additional_options',
                'value' => serialize($additionalOptions),
                'product_id' => $item->getProduct()->getId()
            ]);
        }
    }
}