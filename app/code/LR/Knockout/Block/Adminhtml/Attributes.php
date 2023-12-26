<?php

namespace LR\Knockout\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\AppInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;

class Attributes extends \Magento\Backend\Block\Template
{
    /**
     * Undocumented variable
     *
     * @var [type]
     */
    public $orderRepository;

    /**
     * Undocumented function
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        array $data = []
    ) {
        $this->orderRepository = $orderRepository;
        parent::__construct($context, $data);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getOrder()
    {
        try {
            $orderId = $this->getRequest()->getParam('order_id');
            return $this->orderRepository->get($orderId);
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }
}
