<?php

namespace LR\Knockout\Plugin;

use Magento\Quote\Api\CartRepositoryInterface;

class ShippingInformationManagement
{
    /**
     * Undocumented variable
     *
     * @var [type]
     */
    public $cartRepository;

    /**
     * Undocumented function
     *
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        CartRepositoryInterface $cartRepository
    ) {
        $this->cartRepository = $cartRepository;
    }

    /**
     * Undocumented function
     *
     * @param [type] $subject
     * @param [type] $cartId
     * @param [type] $addressInformation
     * @return void
     */
    public function beforeSaveAddressInformation($subject, $cartId, $addressInformation)
    {
        $quote = $this->cartRepository->getActive($cartId);
        $deliveryNote = $addressInformation->getShippingAddress()->getExtensionAttributes()->getDeliveryNote();
        $quote->setDeliveryNote($deliveryNote);
        $this->cartRepository->save($quote);
        return [$cartId, $addressInformation];
    }
}
