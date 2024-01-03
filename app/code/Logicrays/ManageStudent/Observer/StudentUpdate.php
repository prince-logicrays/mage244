<?php

namespace Logicrays\ManageStudent\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Logicrays\ManageStudent\Model\ResourceModel\ManageStudent\CollectionFactory as ManageStudentCollectionFactory;

class StudentUpdate  implements ObserverInterface
{
    protected $_request;
    protected $managestudentCollectionFactory;

    public function __construct(
        RequestInterface $request,
        ManageStudentCollectionFactory $managestudentCollectionFactory,
        Json $serializer = null
        )
    {
        $this->_request = $request;
        $this->managestudentCollectionFactory = $managestudentCollectionFactory;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $item = $observer->getItem();
        $productParamsData = $this->getStudents();

        if ($this->_request->getFullActionName() == 'checkout_cart_updateItemOptions') { 
            $productParamsData = $this->getStudents();
            $str = implode(",", $productParamsData);
            $additionalOptions = [];

            if (is_array($productParamsData)) {
                        $additionalOptions[] = array(
                        'label' => "Student", //Custom option label
                        'value' => $str, //Custom option value
                    );
            }
            $item->addOption([
                'code' => 'additional_options',
                'value' => serialize($additionalOptions),
                'product_id' => $item->getProduct()->getId()
            ]);
        }

    }

    public function getStudents()
    {
        $studentsId = $this->_request->getParam('students');
        $collection = $this->managestudentCollectionFactory->create();
        $collection->addFieldToFilter("id", $studentsId);
        $studentData = array();
        foreach ($collection as $student) {
            $studentData[] = $student->getFirstName() . " " . $student->getLastName();
        }
        return $studentData;
    }

}