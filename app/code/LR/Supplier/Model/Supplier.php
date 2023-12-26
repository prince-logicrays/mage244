<?php

namespace LR\Supplier\Model;

use LR\Supplier\Model\ResourceModel\Supplier as SupplierResource;
use Magento\Framework\Model\AbstractModel;

class Supplier extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(SupplierResource::class);
    }
}
