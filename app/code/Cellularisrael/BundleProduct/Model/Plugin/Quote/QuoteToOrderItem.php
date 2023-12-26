<?php
namespace Cellularisrael\BundleProduct\Model\Plugin\Quote;

use Closure;

class QuoteToOrderItem
{
    /**
     * @param \Magento\Quote\Model\Quote\Item\ToOrderItem $subject
     * @param callable $proceed
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param array $additional
     * @return \Magento\Sales\Model\Order\Item
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundConvert(
        \Magento\Quote\Model\Quote\Item\ToOrderItem $subject,
        Closure $proceed,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        $additional = []
    ) {
        /** @var $orderItem \Magento\Sales\Model\Order\Item */
        $startDate = $item->getStartDate();
        if ($startDate) {
            $nyDate                = new \DateTime('now', new \DateTimeZone("America/New_York"));
            $formattedNyDate       = $nyDate->format('Y-m-d');
            $selectedDate          = new \DateTime($startDate);
            $formattedSelectedDate = $selectedDate->format('Y-m-d');
            if ($formattedSelectedDate < $formattedNyDate) {
                $startDate = $formattedNyDate;
            }
        }
        $orderItem = $proceed($item, $additional); //result of function 'convert' in class 'Magento\Quote\Model\Quote\Item\ToOrderItem'
        $orderItem->setStartDate($startDate);
        $orderItem->setEndDate($item->getEndDate());
        $orderItem->setIsSubscription($item->getIsSubscription());
        $orderItem->setMerchant($item->getMerchant());
        $orderItem->setPhoneCarrierId($item->getPhoneCarrierId());
        $orderItem->setIsPlan($item->getIsPlan());
        $orderItem->setIsraelPrice($item->getIsraelPrice());
        $orderItem->setDeposit($item->getDeposit());
        $orderItem->setSimCustomAddon($item->getSimCustomAddon());
        $orderItem->setPhoneCustomAddon($item->getPhoneCustomAddon());
        return $orderItem; // return an object '$orderItem' which will replace result of function 'convert' in class 'Magento\Quote\Model\Quote\Item\ToOrderItem'
    }

}
