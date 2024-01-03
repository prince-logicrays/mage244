<?php
namespace Logicrays\ManageStudent\Block\Students;

use Logicrays\ManageStudent\Model\ManageStudentFactory;
use Logicrays\ManageStudent\Helper\Data as Helper;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends \Magento\Framework\View\Element\Template
{
    protected $_customerSession;
    protected $currentCustomer;
    protected $dataObjectHelper;

    protected $_helper;
    protected $_managestudentFactory;
    protected $_email = null;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        Helper $helper,
        ManageStudentFactory $managestudentFactory,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
        $this->currentCustomer = $currentCustomer;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->_helper = $helper;
        $this->_managestudentFactory = $managestudentFactory;

        parent::__construct(
            $context,
            $data
        );
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->initEmailObject();

        $this->pageConfig->getTitle()->set($this->getTitle());

        if ($postedData = $this->_customerSession->getStudentFormData(true)) {
            $this->dataObjectHelper->populateWithArray(
                $this->_email,
                $postedData,
                \Logicrays\ManageStudent\Api\Data\ManageStudentInterface::class
            );
        }
        return $this;
    }
    
    private function initEmailObject()
    {
        // Init address object
        if ($emailId = $this->getRequest()->getParam('id')) {
            try {
                $this->_email = $this->_managestudentFactory->create()->load($emailId);
                if ($this->_email->getCustomerId() != $this->_customerSession->getCustomerId()) {
                    $this->_email = null;
                }
            } catch (NoSuchEntityException $e) {
                $this->_email = null;
            }
        }

        if ($this->_email === null || !$this->_email->getId()) {
            $this->_email = $this->_managestudentFactory->create();
            $this->_email->setCustomerId($this->getCustomer()->getId());
        }
    }

    public function getTitle()
    {
        if ($this->getEmail()->getId()) {
            $title = __('Edit Student');
        } else {
            $title = __('Add New Student');
        }
        return $title;
    }

    public function getBackUrl()
    {
        if ($this->getData('back_url')) {
            return $this->getData('back_url');
        }

        return $this->getUrl('managestudent/student/');
    }

    public function getSaveUrl()
    {
        return $this->_urlBuilder->getUrl(
            'managestudent/student/formPost',
            ['_secure' => true, 'id' => $this->getEmail()->getId()]
        );
    }
    
    public function getEmail()
    {
        return $this->_email;
    }

    public function getCustomer()
    {
        return $this->currentCustomer->getCustomer();
    }

    public function getBackButtonUrl()
    {
        return $this->getUrl('managestudent/student');
    }
}
