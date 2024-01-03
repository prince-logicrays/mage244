<?php

namespace Logicrays\ManageStudent\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Logicrays\ManageStudent\Model\ResourceModel\ManageStudent\CollectionFactory as ManageStudentCollectionFactory;

class CheckoutAddObserver implements ObserverInterface
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

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // Check and set information according to your need
        $product = $observer->getProduct();
        if ($this->_request->getFullActionName() == 'checkout_cart_add') { //checking when product is adding to cart
            $productParamsData = $this->getStudents();
            $str = implode(",", $productParamsData);
            $product = $observer->getProduct();
            $additionalOptions = [];

            if (is_array($productParamsData)) {
                //foreach ($productParamsData as $student) {
                        $additionalOptions[] = array(
                        'label' => "Student", //Custom option label
                        'value' => $str, //Custom option value
                    );
                //}
            }
            $product->addCustomOption('additional_options', $this->serializer->serialize($additionalOptions));
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