<?php

namespace LR\Supplier\Model;

use LR\Supplier\Model\ResourceModel\SupplierItem\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var array
     */
    protected $loadedData;
    // @codingStandardsIgnoreStart
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $supplierItemCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $supplierItemCollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
    // @codingStandardsIgnoreEnd
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $supplierItem) {
            $this->loadedData[$supplierItem->getId()] = $supplierItem->getData();
        }
        return $this->loadedData;
    }
}
