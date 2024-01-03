<?php

namespace Logicrays\ManageStudent\Model\ResourceModel\ManageStudent;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Logicrays\ManageStudent\Model\ResourceModel\ManageStudent as ResourceModel;
use Logicrays\ManageStudent\Model\ManageStudent as Model;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }

     public function addCustomerFilter($customer) {
        if($customer instanceof \Magento\Customer\Api\Data\CustomerInterface) {
            $customerId = $customer->getId();
        } else {
            $customerId = $customer;
        }
        $this->addFieldToFilter("customer_id", array('eq' => $customerId));
    }
}