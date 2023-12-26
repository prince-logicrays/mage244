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

class CategoryOptionImage extends \Magento\Framework\Model\AbstractModel
{
    public const ID = 'id';
    public const CATEGORY_ID = 'category_id';
    public const ATTRIBUTE_OPTIONS = 'attribute_options';
    public const IMAGE = 'image';

    /**
     * CMS page cache tag.
     */
    public const CACHE_TAG = 'logicrays_categoryoptionimage';

    /**
     * @var string
     */
    protected $_cacheTag = 'logicrays_categoryoptionimage';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'logicrays_categoryoptionimage';

    /**
     * _construct function
     *
     * @return Initialize resource model
     */
    protected function _construct()
    {
        $this->_init(\Logicrays\ManageCategoryImage\Model\ResourceModel\CategoryOptionImage::class);
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
     * Get Attribute_options.
     *
     * @return string
     */
    public function getAttributeOptions()
    {
        return $this->getData(self::ATTRIBUTE_OPTIONS);
    }

    /**
     * SetAttributeOptions function
     *
     * @param string $attributeOptions
     * @return string
     */
    public function setAttributeOptions($attributeOptions)
    {
        return $this->setData(self::ATTRIBUTE_OPTIONS, $attributeOptions);
    }

    /**
     * Get Image.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * SetImage function
     *
     * @param string $image
     * @return string
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }
}
