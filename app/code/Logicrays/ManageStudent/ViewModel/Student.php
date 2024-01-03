<?php

namespace Logicrays\ManageStudent\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Logicrays\ManageStudent\Model\ResourceModel\ManageStudent\CollectionFactory as ManageStudentCollectionFactory;
use Logicrays\ManageStudent\Helper\Data;
use Magento\Customer\Model\Session;

class Student implements ArgumentInterface
{
    /**
     * @var Session
     */
    protected $customerSession;
    /**
     * @var CollectionFactory
     */
    protected $managestudentCollectionFactory;
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @param Data $helperData
     */
    public function __construct(
        ManageStudentCollectionFactory $managestudentCollectionFactory,
        Session $customerSession,
        Data $helperData
    ) {
        $this->managestudentCollectionFactory = $managestudentCollectionFactory;
        $this->customerSession = $customerSession;
        $this->helperData = $helperData;
    }

    /**
     * Get helper data
     *
     * @return array
     */
    public function getHelperData()
    {
        return $this->helperData;
    }

    public function getCurrentCustomer()
    {
        return $this->customerSession->getCustomer();
    }

    public function isCustomerLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }

    public function getManageStudentCollection()
    {
        $collection = $this->managestudentCollectionFactory->create();
        $collection->addFieldToFilter('status', 1);
        // $collection->addCustomerFilter($this->getCurrentCustomer()->getId());
        return $collection;
    }
}