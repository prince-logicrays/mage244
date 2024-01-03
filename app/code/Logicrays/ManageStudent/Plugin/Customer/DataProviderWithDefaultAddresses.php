<?php
namespace Logicrays\ManageStudent\Plugin\Customer;

use Logicrays\ManageStudent\Helper\Data as Helper;
use Logicrays\ManageStudent\Model\ResourceModel\ManageStudent\CollectionFactory as CollectionFactory;

class DataProviderWithDefaultAddresses
{
    protected $_helper;
    protected $_collectionFactory;

    public function __construct(
        Helper $helper,
        CollectionFactory $collectionFactory
    ) {
        $this->_helper = $helper;
        $this->_collectionFactory = $collectionFactory;
    }

    public function afterGetData(\Magento\Customer\Model\Customer\DataProviderWithDefaultAddresses $subject, $result)
    {
        if($result){
            $customerId = key($result);
            $storeId = $result[$customerId]['customer']['store_id'];

            if(!$this->_helper->isEnabled($storeId)) {
                return $result;
            }

            $collection = $this->_collectionFactory->create();
            $collection->addFieldToFilter('customer_id', array('eq' => $customerId));

            if($collection->count()) {
                $items = $collection->getItems();
                foreach ($items as $item) {
                    $itemData = $item->getData();
                    $result[$customerId]['managestudent']['container_managestudent']['managestudent']['managestudent'][] = $itemData;
                }
            }
        }
        return $result;
    }

}