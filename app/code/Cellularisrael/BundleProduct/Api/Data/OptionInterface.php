<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cellularisrael\BundleProduct\Api\Data;

/**
 * Interface OptionInterface
 * @api
 * @since 100.0.2
 */
interface OptionInterface extends \Magento\Bundle\Api\Data\OptionInterface
{
    /**
     * Get option store_description
     *
     * @return string|null
     */
    public function getStoreDescription();

    /**
     * Set option store_description
     *
     * @param string $storeDescription
     * @return $this
     */
    public function setStoreDescription($storeDescription);
}
