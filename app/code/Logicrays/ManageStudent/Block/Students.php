<?php
namespace Logicrays\ManageStudent\Block;

use Magento\Customer\Api\AddressRepositoryInterface;
use Logicrays\ManageStudent\Block\Students\Grid as StudentsGrid;
use Logicrays\ManageStudent\Model\Repository\ManageStudentRepository;

class Students extends \Magento\Framework\View\Element\Template
{
    protected $currentCustomer;
    protected $customerRepository;
    protected $managestudentRepository;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository = null,
        ManageStudentRepository $managestudentRepository,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        array $data = [],
        StudentsGrid $studentsGrid = null
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->managestudentRepository = $managestudentRepository;
        $this->studentsGrid = $studentsGrid ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(studentsGrid::class);
        parent::__construct($context, $data);
    }
    
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Manage Students'));
        return parent::_prepareLayout();
    }

    public function getAddEmailUrl()
    {
        return $this->studentsGrid->getAddEmailUrl();
    }
    
    public function getBackUrl()
    {
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return $this->getUrl('customer/account/', ['_secure' => true]);
    }
    
    public function getDeleteUrl()
    {
        return $this->studentsGrid->getDeleteUrl();
    }

    public function getEmailEditUrl($emailId)
    {
        return $this->studentsGrid->getEmailEditUrl($emailId);
    }
    
    public function getCustomer()
    {
        $customer = null;
        try {
            $customer = $this->currentCustomer->getCustomer();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
        }
        return $customer;
    }
}
