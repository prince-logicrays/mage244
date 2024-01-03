<?php

namespace Logicrays\ManageStudent\Observer\Customer;

use Logicrays\ManageStudent\Helper\Data as Helper;
use Logicrays\ManageStudent\Model\ManageStudentFactory as ModelFactory;
use Logicrays\ManageStudent\Model\ResourceModel\ManageStudent\CollectionFactory as CollectionFactory;
use Logicrays\ManageStudent\Api\ManageStudentRepositoryInterface as ModeLogicraysepository;

class Save implements \Magento\Framework\Event\ObserverInterface
{
    protected $_request;

    protected $_helper;
    protected $_repository;
    protected $_modelFactory;
    protected $_collectionFactory;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        Helper $helper,
        ModeLogicraysepository $repository,
        ModelFactory $modelFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->_request = $request;

        $this->_helper = $helper;
        $this->_repository = $repository;
        $this->_modelFactory = $modelFactory;
        $this->_collectionFactory = $collectionFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getCustomer();
        $storeId = $customer->getStoreId();

        if(!$this->_helper->isEnabled($storeId)) {
            return $this;
        }

        $data = $this->_request->getParams();

        $collection = $this->_collectionFactory->create();
        $collection->addFieldToFilter('customer_id', array('eq' => $customer->getId()));
        $currentStudents = array();
        if($collection->count()) {
            foreach($collection as $model) {
                $currentStudents[$model->getId()] = $model;
            }
        }
        /**
         * Save manage students
         * If manage students tab is active
         * remove ['managestudent']['container_managestudent]
         * from $data['managestudent']['container_managestudent']['managestudent']
         */
        if($data && isset($data['managestudent']['container_managestudent']['managestudent'])) {
            $studentData = $data['managestudent']['container_managestudent']['managestudent'];
            if(array_key_exists('managestudent', $studentData)){
                $additionalemailsEmails = $studentData['managestudent'];
                foreach($additionalemailsEmails as $email) {
                    if(isset($email['id'])) {
                        $model = $currentStudents[$email['id']];
                        unset($currentStudents[$email['id']]);
                    } else {
                        $model = $this->_modelFactory->create();
                        $model->setCustomerId($customer->getId());
                    }
                    
                    if(!$email['status']) {
                        $email['status'] = 0;
                    }

                    $model->setEmail(trim($email['email']));
                    $model->setFirstName(trim($email['first_name']));
                    $model->setLastName(trim($email['last_name']));
                    $model->setGrade(trim($email['grade']));
                    $model->setStatus($email['status']);
                    $this->_repository->save($model);
                }
            }
        }

        if($currentStudents && sizeof($currentStudents)) {
            foreach($currentStudents as $model) {
                $this->_repository->delete($model);
            }
        }

        return $this;
    }
}