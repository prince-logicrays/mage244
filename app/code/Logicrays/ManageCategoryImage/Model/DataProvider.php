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

use Logicrays\ManageCategoryImage\Model\ResourceModel\ManageCategoryImage\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * _loadedData variable
     *
     * @var array
     */
    protected $_loadedData;

    /**
     * __construct function
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $manageCategoryImageCollectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $manageCategoryImageCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $manageCategoryImageCollectionFactory->create();

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * GetData function
     *
     * @return Array
     */
    public function getData()
    {
        if (isset($this->_loadedData)) {
            return $this->_loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $customer) {
            $this->_loadedData[$customer->getId()] = $customer->getData();
        }
        return $this->_loadedData;
    }
}
