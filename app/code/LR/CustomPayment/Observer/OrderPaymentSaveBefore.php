<?php

namespace LR\CustomPayment\Observer;

class OrderPaymentSaveBefore implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Construct
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Serialize\Serializer\Serialize $serialize
     * @param \Magento\Webapi\Controller\Rest\InputParamsResolver $inputParamsResolver
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Serialize\Serializer\Serialize $serialize,
        \Magento\Webapi\Controller\Rest\InputParamsResolver $inputParamsResolver,
        \Magento\Framework\App\State $state
    ) {
        $this->order = $order;
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
        $this->_serialize = $serialize;
        $this->inputParamsResolver = $inputParamsResolver;
        $this->_state = $state;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getOrder();
        $inputParams = $this->inputParamsResolver->resolve();
        if ($this->_state->getAreaCode() != \Magento\Framework\App\Area::AREA_ADMINHTML) {
            foreach ($inputParams as $inputParam) {
                if ($inputParam instanceof \Magento\Quote\Model\Quote\Payment) {
                    $paymentData = $inputParam->getData('additional_data');
                    $quote = $this->quoteRepository->get($order->getQuoteId());
                    $method = $quote->getPayment()->getMethodInstance()->getCode();
                    if ($method == 'custompayment') {
                        if (isset($paymentData['instruction'])) {
                            $order->setData('instruction', $paymentData['instruction']);
                        }
                    }
                }
            }
        }
    }
}
