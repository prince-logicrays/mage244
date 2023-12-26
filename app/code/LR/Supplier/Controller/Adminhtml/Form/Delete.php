<?php

namespace LR\Supplier\Controller\Adminhtml\Form;

use Magento\Backend\App\Action\Context;
use LR\Supplier\Model\SupplierItem;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var SupplierItem
     */
    protected $modelFactory;

    /**
     * @param Context $context
     * @param SupplierItem $modelFactory
     */
    public function __construct(
        Context $context,
        SupplierItem $modelFactory
    ) {
        parent::__construct($context);
        $this->modelFactory = $modelFactory;
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('entity_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->modelFactory;
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('Record deleted successfully.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/form/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addError(__('Record does not exist.'));
        return $resultRedirect->setPath('*/*/');
    }
}
