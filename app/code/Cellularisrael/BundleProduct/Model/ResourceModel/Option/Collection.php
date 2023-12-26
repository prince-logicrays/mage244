<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cellularisrael\BundleProduct\Model\ResourceModel\Option;

use Magento\Catalog\Model\Product\Attribute\Source\Status;

/**
 * Bundle Options Resource Collection
 * @api
 * @since 100.0.2
 */
class Collection extends \Magento\Bundle\Model\ResourceModel\Option\Collection
{
    /**
     * Joins values to options
     *
     * @param int $storeId
     * @return $this
     */
    public function joinValues($storeId)
    {
        $this->getSelect()->joinLeft(
            ['option_value_default' => $this->getTable('catalog_product_bundle_option_value')],
            implode(
                ' AND ',
                [
                    'main_table.option_id = option_value_default.option_id',
                    'main_table.parent_id = option_value_default.parent_product_id',
                    'option_value_default.store_id = 0'
                ]
            ),
            []
        )->columns(
            ['default_title' => 'option_value_default.title'],
        )->columns(
            ['default_store_description' => 'option_value_default.store_description']
        );

        $title = $this->getConnection()->getCheckSql(
            'option_value.title IS NOT NULL',
            'option_value.title',
            'option_value_default.title'
        );

        $storeDescription = $this->getConnection()->getCheckSql(
            'option_value.store_description IS NOT NULL',
            'option_value.store_description',
            'option_value_default.store_description'
        );
        if ($storeId !== null) {
            $this->getSelect()
            ->columns(
                ['title' => $title]
            )->columns(
                ['store_description' => $storeDescription]
            )->joinLeft(
                ['option_value' => $this->getTable('catalog_product_bundle_option_value')],
                $this->getConnection()->quoteInto(
                    implode(
                        ' AND ',
                        [
                            'main_table.option_id = option_value.option_id',
                            'main_table.parent_id = option_value.parent_product_id',
                            'option_value.store_id = ?'
                        ]
                    ),
                    $storeId
                ),
                []
            );
        }
        return $this;
    }
}
