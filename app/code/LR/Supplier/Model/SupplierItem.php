<?php

namespace LR\Supplier\Model;

use LR\Supplier\Model\ResourceModel\SupplierItem as SupplierItemResource;
use Magento\Framework\Model\AbstractModel;

class SupplierItem extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(SupplierItemResource::class);
    }
}
