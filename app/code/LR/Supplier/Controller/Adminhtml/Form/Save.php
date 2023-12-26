<?php

namespace LR\Supplier\Controller\Adminhtml\Form;

use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use LR\Supplier\Model\SupplierItem;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var SupplierItem
     */
    protected $supplierItememodel;

    /**
     * @var Session
     */
    protected $adminsession;

    /**
     * @param Action\Context $context
     * @param SupplierItem $supplierItememodel
     * @param Session $adminsession
     */
    public function __construct(
        Action\Context $context,
        SupplierItem $supplierItememodel,
        Session $adminsession
    ) {
        parent::__construct($context);
        $this->supplierItememodel = $supplierItememodel;
        $this->adminsession = $adminsession;
    }

    /**
     * Save supplier item record action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $entityId = $this->getRequest()->getParam('entity_id');
            if ($entityId) {
                $this->supplierItememodel->load($entityId);
            }
            $this->supplierItememodel->setData($data);
            try {
                $this->supplierItememodel->save();
                $this->messageManager->addSuccess(__('The data has been saved.'));
                $this->adminsession->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    if ($this->getRequest()->getParam('back') == 'add') {
                        return $resultRedirect->setPath('*/form/add');
                    } else {
                        return $resultRedirect->setPath('*/form/edit', ['entity_id' => $this->supplierItememodel->getEntityId(), '_current' => true]);
                    }
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
            }
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/form/edit', ['entity_id' => $this->getRequest()->getParam('entity_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
