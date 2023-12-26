<?php

namespace LR\Supplier\Model\ResourceModel\Supplier;

use LR\Supplier\Model\Supplier as SupplierModel;
use LR\Supplier\Model\ResourceModel\Supplier as SupplierResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(SupplierModel::class, SupplierResource::class);
    }
}
