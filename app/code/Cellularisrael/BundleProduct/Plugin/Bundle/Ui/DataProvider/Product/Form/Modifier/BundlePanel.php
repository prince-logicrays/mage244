<?php

namespace Cellularisrael\BundleProduct\Plugin\Bundle\Ui\DataProvider\Product\Form\Modifier;

use Magento\Ui\Component\Form;

class BundlePanel
{
    /**
     * @param \Magento\Bundle\Ui\DataProvider\Product\Form\Modifier\BundlePanel $subject
     * @param $meta
     * @return mixed
     */
    public function afterModifyMeta(\Magento\Bundle\Ui\DataProvider\Product\Form\Modifier\BundlePanel $subject, $meta)
    {
        $fieldSet = [
            'frontend_type' => [
                'dataType' => Form\Element\DataType\Boolean::NAME,
                'formElement'   => Form\Element\Select::NAME,
                'label' => 'Frontend Type',
                'dataScope' => 'frontend_type',
                'sortOrder' => 30,
                'options' => [
                    [
                        'label' => __('Option'),
                        'value' => 'option'
                    ],
                    [
                        'label' => __('Grid'),
                        'value' => 'grid'
                    ],
                ],
            ],
            'description' => [
                'dataType' => Form\Element\DataType\Text::NAME,
                'formElement'   => Form\Element\Textarea::NAME,
                'label' => 'Description',
                'dataScope' => 'description',
                'sortOrder' => 40
            ],
            'merchant' => [
                'dataType' => Form\Element\DataType\Boolean::NAME,
                'formElement'   => Form\Element\Select::NAME,
                'label' => 'Merchant',
                'dataScope' => 'merchant',
                'sortOrder' => 50,
                'options' => [
                    [
                        'label' => __('Cellular Israel'),
                        'value' => 1
                    ],
                    [
                        'label' => __('Provider'),
                        'value' => 2
                    ],
                ],
            ],
            'sim_custom_addon' => [
                'dataType' => Form\Element\DataType\Boolean::NAME,
                'formElement'   => Form\Element\Select::NAME,
                'label' => 'sim_custom_addon',
                'dataScope' => 'sim_custom_addon',
                'sortOrder' => 60,
                'options' => [
                    
                    [
                        'label' => __(' No '),
                        'value' => 0
                    ],
                    [
                        'label' => __('Yes'),
                        'value' => 1
                    ],
                ],
            ],

            'phone_custom_addon' => [
                'dataType' => Form\Element\DataType\Boolean::NAME,
                'formElement'   => Form\Element\Select::NAME,
                'label' => 'Phone Custom Addon',
                'dataScope' => 'phone_custom_addon',
                'sortOrder' => 70,
                'options' => [
                    
                    [
                        'label' => __(' No '),
                        'value' => 0
                    ],
                    [
                        'label' => __('Yes'),
                        'value' => 1
                    ],
                ],
            ],
        ];

        foreach ($fieldSet as $filed => $fieldOptions)
        {
            $meta['bundle-items']['children']['bundle_options']['children']
            ['record']['children']['product_bundle_container']['children']['option_info']['children'][$filed] = $this->getSelectionCustomText($fieldOptions);
        }

        return $meta;
    }

    /**
     * @param $fieldOptions
     * @return array
     */
    protected function getSelectionCustomText($fieldOptions)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Form\Field::NAME,
                        'dataType'      => $fieldOptions['dataType'],
                        'formElement'   => $fieldOptions['formElement'],
                        'label'         => __($fieldOptions['label']),
                        'dataScope'     => $fieldOptions['dataScope'],
                        'sortOrder'     => $fieldOptions['sortOrder'],
                        'options'       => array_key_exists('options', $fieldOptions) ? $fieldOptions['options']: "",
                    ]
                ]
            ]
        ];
    }
}