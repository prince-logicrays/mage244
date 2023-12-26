<?php

namespace LR\CustomPayment\Block\Adminhtml\Order\View;

use Magento\Backend\Block\Template\Context;
use Magento\Sales\Model\OrderRepository;

class Custom extends \Magento\Backend\Block\Template
{
    public function __construct(
        Context $context,
        OrderRepository $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
        parent::__construct($context);
    }

    public function getOrder()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = $this->orderRepository->get($orderId);
        return $order;
    }
}
