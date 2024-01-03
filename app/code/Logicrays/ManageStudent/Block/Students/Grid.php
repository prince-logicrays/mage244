<?php
declare(strict_types=1);

namespace Logicrays\ManageStudent\Block\Students;

use Logicrays\ManageStudent\Model\ResourceModel\ManageStudent\CollectionFactory as ManageStudentCollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class Grid extends \Magento\Framework\View\Element\Template
{
    private $currentCustomer;
    private $managestudentCollectionFactory;
    private $managestudentCollection;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        ManageStudentCollectionFactory $managestudentCollectionFactory,
        array $data = []
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->managestudentCollectionFactory = $managestudentCollectionFactory;

        parent::__construct($context, $data);
    }

    protected function _prepareLayout(): void
    {
        parent::_prepareLayout();
        $this->preparePager();
    }

    public function getAddStudentUrl(): string
    {
        return $this->getUrl('managestudent/student/new', ['_secure' => true]);
    }
    
    public function getDeleteUrl($emailId): string
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $key_form = $objectManager->get('Magento\Framework\Data\Form\FormKey');
        $formKey = $key_form->getFormKey();
        return $this->getUrl('managestudent/student/delete', ['_secure' => true, 'id' => $emailId, 'form_key' => $formKey]);
    }
    
    public function getStudentEditUrl($emailId): string
    {
        return $this->getUrl('managestudent/student/edit', ['_secure' => true, 'id' => $emailId]);
    }

    public function getCustomer(): \Magento\Customer\Api\Data\CustomerInterface
    {
        $customer = $this->getData('customer');
        if ($customer === null) {
            $customer = $this->currentCustomer->getCustomer();
            $this->setData('customer', $customer);
        }
        return $customer;
    }

    private function preparePager(): void
    {
        $managestudentCollection = $this->getManageStudentCollection();
        if (null !== $managestudentCollection) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'managestudent.students.pager'
            )->setCollection($managestudentCollection);
            $this->setChild('pager', $pager);
        }
    }

    public function getManageStudentCollection(): \Logicrays\ManageStudent\Model\ResourceModel\ManageStudent\Collection
    {
        if (null === $this->managestudentCollection) {
            if (null === $this->getCustomer()) {
                throw new NoSuchEntityException(__('Customer not logged in'));
            }

            $collection = $this->managestudentCollectionFactory->create();
            $collection->addCustomerFilter($this->getCustomer()->getId());
            $this->managestudentCollection = $collection;
        }
        return $this->managestudentCollection;
    }
}
