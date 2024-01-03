<?php

namespace Logicrays\ManageStudent\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ManageStudent extends AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('manage_student', 'id');
    }
}