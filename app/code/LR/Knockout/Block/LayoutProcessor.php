<?php

namespace LR\Knockout\Block;

use LR\Knockout\Helper\Data;

class LayoutProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
    /**
     * @var Data
     */
    protected $data;

    /**
     * @param Data $data
     */
    public function __construct(
        Data $data
    ) {
        $this->data = $data;
    }

    /**
     * Undocumented function
     *
     * @param [type] $jsLayout
     * @return void
     */
    public function process($jsLayout)
    {
        $moduleEnable =  $this->data->isEnable();
        if ($moduleEnable == 1) {
            $attributeCode = 'delivery_note';
            $fieldConfiguration = [
                'component' => 'Magento_Ui/js/form/element/textarea',
                'config' => [
                    'customScope' => 'shippingAddress.extension_attributes',
                    'customEntry' => null,
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/textarea',
                    'tooltip' => [
                        'description' => 'Here you can leave delivery notes',
                    ],
                ],
                'dataScope' => 'shippingAddress.extension_attributes' . '.' . $attributeCode,
                'label' => 'Delivery Notes',
                'provider' => 'checkoutProvider',
                'sortOrder' => 1000,
                'validation' => [
                    'required-entry' => true
                ],
                'options' => [],
                'filterBy' => null,
                'customEntry' => null,
                'visible' => true,
                'value' => ''
            ];
            $jsLayout['components']['checkout']['children']
            ['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']
            ['children'][$attributeCode] = $fieldConfiguration;
        }
            return $jsLayout;
    }
}
