<?php

namespace Cellularisrael\BundleProduct\Plugin\Catalog\Model;

class Config
{
    public function afterGetAttributeUsedForSortByArray(
    \Magento\Catalog\Model\Config $catalogConfig,
    $options
    ) {

        $options['low_to_high'] = __('Price: Low to High');
        $options['high_to_low'] = __('Price: High to Low');
        return $options;

    }

}