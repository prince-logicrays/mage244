<?php
namespace Cellularisrael\BundleProduct\Plugin\Ui\DataProvider\Product\Form\Modifier;

use Magento\Ui\Component\Form;
use Magento\Bundle\Ui\DataProvider\Product\Form\Modifier\BundleCustomOptions as MagentoBundleCustomOptions;

class BundleCustomOptions
{
    public const FIELD_CUSTOM_FIELD_BUNDLE_POPUP_MESSAGE = 'bundle_popup_message';

    /**
     * After Modify Meta function
     *
     * @param MagentoBundleCustomOptions $subject
     * @param array $meta
     * @return array
     */
    public function afterModifyMeta(MagentoBundleCustomOptions $subject, array $meta)
    {
        if (isset($meta['bundle-items']['children']['bundle_options']['children']['record']['children']
            ['product_bundle_container']['children']['bundle_selections']['children']['record']['children'])) {

            $meta['bundle-items']['children']['bundle_options']['children']['record']['children']
                ['product_bundle_container']['children']['bundle_selections']['children']['record']['children']
                [static::FIELD_CUSTOM_FIELD_BUNDLE_POPUP_MESSAGE] =
                $this->getBundlePopupMessageFieldOptionFieldConfig(99);
            
            // Reorder table headings
            $action_delete = $meta['bundle-items']['children']['bundle_options']['children']['record']['children']
            ['product_bundle_container']['children']['bundle_selections']['children']['record']['children']
            ['action_delete'];

            unset($meta['bundle-items']['children']['bundle_options']['children']['record']['children']
            ['product_bundle_container']['children']['bundle_selections']['children']['record']['children']
            ['action_delete']);

            $meta['bundle-items']['children']['bundle_options']['children']['record']['children']
            ['product_bundle_container']['children']['bundle_selections']['children']['record']['children']
            ['action_delete'] = $action_delete;
        }

        return $meta;
    }

    /**
     * Get bundle option popup message config function
     *
     * @param mixed $sortOrder
     * @return array
     */
    protected function getBundlePopupMessageFieldOptionFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Message'),
                        'componentType' => Form\Field::NAME,
                        'dataType' => Form\Element\DataType\Text::NAME,
                        'formElement'   => Form\Element\Textarea::NAME,
                        'dataScope' => static::FIELD_CUSTOM_FIELD_BUNDLE_POPUP_MESSAGE,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];
    }
}
