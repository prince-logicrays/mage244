<?php

namespace LR\Supplier\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class SupplierItem extends AbstractDb
{
    /**
     * Construct.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $resourcePrefix = null
    ) {
        parent::__construct($context,$resourcePrefix);
    }

    protected function _construct()
    {
        $this->_init('lr_supplier', 'entity_id');
    }
}
