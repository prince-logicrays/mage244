<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Cellularisrael\BundleProduct\Ui\DataProvider\Product\Form\Modifier;

use Magento\Bundle\Model\Product\Attribute\Source\Shipment\Type as ShipmentType;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Config\Source\ProductPriceOptionsInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Modal;
use Magento\Store\Model\Store;

/**
 * Create Ship Bundle Items and Affect Bundle Product Selections fields
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BundlePanel extends \Magento\Bundle\Ui\DataProvider\Product\Form\Modifier\BundlePanel
{
    const GROUP_CONTENT = 'content';
    const CODE_SHIPMENT_TYPE = 'shipment_type';
    const CODE_BUNDLE_DATA = 'bundle-items';
    const CODE_AFFECT_BUNDLE_PRODUCT_SELECTIONS = 'affect_bundle_product_selections';
    const CODE_BUNDLE_HEADER = 'bundle_header';
    const CODE_BUNDLE_OPTIONS = 'bundle_options';
    const SORT_ORDER = 20;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var ShipmentType
     */
    protected $shipmentType;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @param LocatorInterface $locator
     * @param UrlInterface $urlBuilder
     * @param ShipmentType $shipmentType
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        LocatorInterface $locator,
        UrlInterface $urlBuilder,
        ShipmentType $shipmentType,
        ArrayManager $arrayManager
    ) {
        $this->locator = $locator;
        $this->urlBuilder = $urlBuilder;
        $this->shipmentType = $shipmentType;
        $this->arrayManager = $arrayManager;
    }

    /**
     * Get configuration for option title
     *
     * @return array
     */
    protected function getTitleConfiguration()
    {
        $result['title']['arguments']['data']['config'] = [
            'dataType' => Form\Element\DataType\Text::NAME,
            'formElement' => Form\Element\Input::NAME,
            'componentType' => Form\Field::NAME,
            'dataScope' => $this->isDefaultStore() ? 'title' : 'default_title',
            'label' => $this->isDefaultStore() ? __('Option Title') : __('Default Title'),
            'sortOrder' => 10,
            'validation' => ['required-entry' => true],
        ];

        if (!$this->isDefaultStore()) {
            $result['store_title']['arguments']['data']['config'] = [
                'dataType' => Form\Element\DataType\Text::NAME,
                'formElement' => Form\Element\Input::NAME,
                'componentType' => Form\Field::NAME,
                'dataScope' => 'title',
                'label' => __('Store View Title'),
                'sortOrder' => 15,
                'validation' => ['required-entry' => true],
            ];
            $result['store_description']['arguments']['data']['config'] = [
                'dataType' => Form\Element\DataType\Text::NAME,
                'formElement' => Form\Element\Input::NAME,
                'componentType' => Form\Field::NAME,
                'dataScope' => 'store_description',
                'label' => __('Store View Description'),
                'sortOrder' => 45,
                'validation' => ['required-entry' => true],
            ];
        }

        return $result;
    }
}
