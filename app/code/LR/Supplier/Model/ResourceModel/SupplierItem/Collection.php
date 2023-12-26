<?php

namespace LR\Supplier\Model\ResourceModel\SupplierItem;

use LR\Supplier\Model\SupplierItem as SupplierItemModel;
use LR\Supplier\Model\ResourceModel\SupplierItem as SupplierItemResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(SupplierItemModel::class, SupplierItemResource::class);
    }
}
