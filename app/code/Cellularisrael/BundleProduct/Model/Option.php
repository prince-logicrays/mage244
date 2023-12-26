<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cellularisrael\BundleProduct\Model;

/**
 * Bundle Option Model
 *
 * @api
 * @method int getParentId()
 * @method null|\Magento\Catalog\Model\Product[] getSelections()
 * @method Option setParentId(int $value)
 * @since 100.0.2
 */
class Option extends \Magento\Bundle\Model\Option
{
    /**#@+
     * Constants
     */
    public const KEY_STORE_DESCRIPTION = 'store_description';
    /**#@-*/

    //@codeCoverageIgnoreStart

    /**
     * Get option store_description
     *
     * @return $this
     */
    public function getStoreDescription()
    {
        return $this->getData(self::KEY_STORE_DESCRIPTION);
    }

    /**
     * Set option store_description
     *
     * @param string $storeDescription
     * @return $this
     */
    public function setStoreDescription($storeDescription)
    {
        return $this->setData(self::KEY_STORE_DESCRIPTION, $storeDescription);
    }

    //@codeCoverageIgnoreEnd
}
