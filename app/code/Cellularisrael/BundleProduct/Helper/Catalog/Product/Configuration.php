<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cellularisrael\BundleProduct\Helper\Catalog\Product;

use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;

/**
 * Helper for fetching properties by product configuration item
 * @api
 * @since 100.0.2
 */
class Configuration extends \Magento\Bundle\Helper\Catalog\Product\Configuration
{
    /**
     * Core data
     *
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    /**
     * Catalog product configuration
     *
     * @var \Magento\Catalog\Helper\Product\Configuration
     */
    protected $productConfiguration;

    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * Serializer interface instance.
     *
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    protected $bundleProductHelper;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Helper\Product\Configuration $productConfiguration
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Helper\Product\Configuration $productConfiguration,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null,
        \Cellularisrael\BundleProduct\Helper\Data $bundleProductHelper
    ) {
        $this->productConfiguration = $productConfiguration;
        $this->pricingHelper        = $pricingHelper;
        $this->escaper              = $escaper;
        $this->serializer           = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
        $this->bundleProductHelper = $bundleProductHelper;
        parent::__construct($context, $productConfiguration, $pricingHelper, $escaper, $serializer);
    }

    /**
     * Get selection quantity
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param int $selectionId
     * @return float
     */
    public function getSelectionQty(\Magento\Catalog\Model\Product $product, $selectionId)
    {
        $selectionQty = $product->getCustomOption('selection_qty_' . $selectionId);
        if ($selectionQty) {
            return $selectionQty->getValue();
        }
        return 0;
    }

    /**
     * Obtain final price of selection in a bundle product
     *
     * @param ItemInterface $item
     * @param \Magento\Catalog\Model\Product $selectionProduct
     * @return float
     */
    public function getSelectionFinalPrice(ItemInterface $item, \Magento\Catalog\Model\Product $selectionProduct)
    {
        $selectionProduct->unsetData('final_price');

        $product = $item->getProduct();
        /** @var \Magento\Bundle\Model\Product\Price $price */
        $price = $product->getPriceModel();

        return $price->getSelectionFinalTotalPrice(
            $product,
            $selectionProduct,
            $item->getQty(),
            $this->getSelectionQty($product, $selectionProduct->getSelectionId()),
            false,
            true
        );
    }

    /**
     * Get bundled selections (slections-products collection)
     *
     * Returns array of options objects.
     * Each option object will contain array of selections objects
     *
     * @param ItemInterface $item
     * @return array
     */
    public function getBundleOptions(ItemInterface $item)
    {
        $options = [];
        $product = $item->getProduct();

        /** @var \Magento\Bundle\Model\Product\Type $typeInstance */
        $typeInstance = $product->getTypeInstance();

        // get bundle options
        $optionsQuoteItemOption = $item->getOptionByCode('bundle_option_ids');
        $bundleOptionsIds       = $optionsQuoteItemOption
        ? $this->serializer->unserialize($optionsQuoteItemOption->getValue())
        : [];

        if ($bundleOptionsIds) {
            /** @var \Magento\Bundle\Model\ResourceModel\Option\Collection $optionsCollection */
            $optionsCollection = $typeInstance->getOptionsByIds($bundleOptionsIds, $product);

            // get and add bundle selections collection
            $selectionsQuoteItemOption = $item->getOptionByCode('bundle_selection_ids');

            $bundleSelectionIds = $this->serializer->unserialize($selectionsQuoteItemOption->getValue());

            if (!empty($bundleSelectionIds)) {
                $selectionsCollection = $typeInstance->getSelectionsByIds($bundleSelectionIds, $product);

                $bundleOptions = $optionsCollection->appendSelections($selectionsCollection, true);
                foreach ($bundleOptions as $bundleOption) {
                    if ($bundleOption->getSelections()) {
                        $option = ['label' => $bundleOption->getTitle(), 'value' => [], 'data' => []];

                        $bundleSelections = $bundleOption->getSelections();

                        foreach ($bundleSelections as $bundleSelection) {

                            $qty = $this->getSelectionQty($product, $bundleSelection->getSelectionId()) * 1;
                            if ($qty) {
                                if (!$bundleSelection->getIsPlan()) {
                                    $option['data'][] = array(
                                        'qty'      => $qty,
                                        'name'     => $this->escaper->escapeHtml($bundleSelection->getName()),
                                        'price'    => $this->pricingHelper->currency($this->getSelectionFinalPrice($item, $bundleSelection)),
                                        'product'  => $bundleSelection,
                                        'merchant' => $bundleOption->getMerchant(),
                                    );
                                }

                                $option['value'][] = $qty . ' x '
                                . $this->escaper->escapeHtml($bundleSelection->getName())
                                . ' '
                                . $this->pricingHelper->currency(
                                    $this->getSelectionFinalPrice($item, $bundleSelection)
                                );
                                $option['has_html'] = true;
                                //$option['custom_view'] = true;
                            }
                        }

                        if ($option['value']) {
                            $options[] = $option;
                        }
                    }
                }
            }
        }

        return $options;
    }

    /**
     * Retrieves product options list
     *
     * @param ItemInterface $item
     * @return array
     */
    public function getOptions(ItemInterface $item)
    {
        return array_merge(
            $this->getBundleOptions($item),
            $this->productConfiguration->getCustomOptions($item)
        );
    }

    public function getBundleOptionsForMinicart(ItemInterface $item)
    {
        $options = [];
        $product = $item->getProduct();
        $itemQty = $item->getQty();

        /** @var \Magento\Bundle\Model\Product\Type $typeInstance */
        $typeInstance = $product->getTypeInstance();

        // get bundle options
        $optionsQuoteItemOption = $item->getOptionByCode('bundle_option_ids');
        $bundleOptionsIds       = $optionsQuoteItemOption
        ? $this->serializer->unserialize($optionsQuoteItemOption->getValue())
        : [];

        if ($bundleOptionsIds) {
            /** @var \Magento\Bundle\Model\ResourceModel\Option\Collection $optionsCollection */
            $optionsCollection = $typeInstance->getOptionsByIds($bundleOptionsIds, $product);

            // get and add bundle selections collection
            $selectionsQuoteItemOption = $item->getOptionByCode('bundle_selection_ids');

            $bundleSelectionIds = $this->serializer->unserialize($selectionsQuoteItemOption->getValue());

            if (!empty($bundleSelectionIds)) {
                $selectionsCollection = $typeInstance->getSelectionsByIds($bundleSelectionIds, $product);

                $bundleOptions = $optionsCollection->appendSelections($selectionsCollection, true);
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $currencysymbol = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
                $currency = $currencysymbol->getStore()->getCurrentCurrencyCode();
                $currencyconvert = $objectManager->create('Magento\Directory\Model\Currency')->load('USD');
                foreach ($bundleOptions as $bundleOption) {
                    if ($bundleOption->getSelections()) {
                        $option           = ['value' => []];
                        $bundleSelections = $bundleOption->getSelections();
                        foreach ($bundleSelections as $bundleSelection) {
                            $qty = $this->getSelectionQty($product, $bundleSelection->getSelectionId()) * 1;
                            if ($qty) {
                                if ($bundleOption->getMerchant() == 2 && $bundleSelection->getIsraelPrice()) {
                                    $option['value'][] = array(
                                        'qty'              => $qty,
                                        'sku'              => $bundleSelection->getSku(),
                                        'final_price'      => $bundleSelection->getIsraelPrice(),
                                        'currency_symbol'  => $this->bundleProductHelper->getCurrencySymbol('ILS'),
                                        'name'             => $this->escaper->escapeHtml($bundleSelection->getName()),
                                        'merchant'         => $bundleOption->getMerchant(),
                                        'is_subscription'  => $bundleSelection->getIsSubscription() ? 1 : 0,
                                        'calculated_price' => number_format(($itemQty * ($qty * $bundleSelection->getIsraelPrice())), 2),
                                    );
                                } else {
                                    $option['value'][] = array(
                                        'qty'              => $qty,
                                        'sku'              => $bundleSelection->getSku(),
                                        'final_price'      => $bundleSelection->getFinalPrice(),
                                        'currency_symbol'  => $this->bundleProductHelper->getCurrencySymbol($currency),
                                        'name'             => $this->escaper->escapeHtml($bundleSelection->getName()),
                                        'merchant'         => $bundleOption->getMerchant(),
                                        'is_subscription'  => $bundleSelection->getIsSubscription() ? 1 : 0,
                                        // 'calculated_price' => number_format($currencyconvert->convert(($itemQty * ($qty * $bundleSelection->getFinalPrice())),$currency), 2),
                                        'calculated_price' => number_format(($itemQty * ($qty * $bundleSelection->getFinalPrice())), 2),
                                      );
                                }
                            }
                        }

                        if ($option['value']) {
                            $options[] = $option;
                        }
                    }
                }
            }
        }

        return $options;
    }
}
