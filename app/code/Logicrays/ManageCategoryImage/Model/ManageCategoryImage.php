<?php
declare(strict_types=1);
/**
 * Logicrays
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Logicrays
 * @package     Logicrays_ManageCategoryImage
 * @copyright   Copyright (c) Logicrays (https://www.logicrays.com/)
 */

namespace Logicrays\ManageCategoryImage\Model;

class ManageCategoryImage extends \Magento\Framework\Model\AbstractModel
{
    public const ID = 'id';
    public const CATEGORY_ID = 'category_id';
    public const ATTRIBUTES = 'attributes';

    /**
     * CMS page cache tag.
     */
    public const CACHE_TAG = 'logicrays_managecategoryimage';

    /**
     * @var string
     */
    protected $_cacheTag = 'logicrays_managecategoryimage';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'logicrays_managecategoryimage';

    /**
     * _construct function
     *
     * @return Initialize resource model
     */
    protected function _construct()
    {
        $this->_init(\Logicrays\ManageCategoryImage\Model\ResourceModel\ManageCategoryImage::class);
    }

    /**
     * Get Id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * SetId function
     *
     * @param int $id
     * @return integer
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get CategoryId.
     *
     * @return int
     */
    public function getCategoryId()
    {
        return $this->getData(self::CATEGORY_ID);
    }

    /**
     * Set CategoryId function
     *
     * @param int $categoryId
     * @return integer
     */
    public function setCategoryId($categoryId)
    {
        return $this->setData(self::CATEGORY_ID, $categoryId);
    }

    /**
     * Get Attributes.
     *
     * @return string
     */
    public function getAttributes()
    {
        return $this->getData(self::ATTRIBUTES);
    }

    /**
     * SetAttributes function
     *
     * @param string $attributes
     * @return string
     */
    public function setAttributes($attributes)
    {
        return $this->setData(self::ATTRIBUTES, $attributes);
    }
}
