<?php

namespace Logicrays\ManageStudent\Controller;

use Magento\Framework\App\RequestInterface;
use Logicrays\ManageStudent\Model\Repository\ManageStudentRepository;
use Logicrays\ManageStudent\Model\ManageStudentFactory;
use Logicrays\ManageStudent\Helper\Data as Helper;

abstract class ManageStudent extends \Magento\Framework\App\Action\Action
{
    protected $_customerSession;
    protected $_helper;
    protected $_managestudentRepository;
    protected $_managestudentFactory;
    protected $_dataProcessor;
    protected $dataObjectHelper;
    protected $resultForwardFactory;
    protected $resultPageFactory;
    protected $_formKeyValidator;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        Helper $helper,
        ManageStudentRepository $managestudentRepository,
        ManageStudentFactory $managestudentFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataProcessor,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
    ) {
        $this->_customerSession = $customerSession;
        $this->_helper = $helper;
        $this->_managestudentRepository = $managestudentRepository;
        $this->_managestudentFactory = $managestudentFactory;
        $this->_dataProcessor = $dataProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->_formKeyValidator = $formKeyValidator;
        parent::__construct($context);
    }

    protected function _getSession()
    {
        return $this->_customerSession;
    }

    public function dispatch(RequestInterface $request)
    {
        if (!$this->_getSession()->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }
        return parent::dispatch($request);
    }

    protected function _buildUrl($route = '', $params = [])
    {
        $urlBuilder = $this->_objectManager->create(\Magento\Framework\UrlInterface::class);
        return $urlBuilder->getUrl($route, $params);
    }
}
