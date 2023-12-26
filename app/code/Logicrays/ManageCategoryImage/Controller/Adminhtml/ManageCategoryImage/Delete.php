<?php
declare(strict_types=1);
/**
 * Logicrays
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Logicrays
 * @package     Logicrays_ManageCategoryImage
 * @copyright   Copyright (c) Logicrays (https://www.logicrays.com/)
 */

namespace Logicrays\ManageCategoryImage\Controller\Adminhtml\ManageCategoryImage;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Logicrays\ManageCategoryImage\Model\ManageCategoryImageFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Delete extends Action implements HttpGetActionInterface
{
    /**
     * ResultPageFactory variable
     *
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * ManageCategoryImageFactory variable
     *
     * @var ManageCategoryImageFactory
     */
    protected $manageCategoryImageFactory;

    /**
     * __construct function
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ManageCategoryImageFactory $manageCategoryImageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ManageCategoryImageFactory $manageCategoryImageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->manageCategoryImageFactory = $manageCategoryImageFactory;
        parent::__construct($context);
    }

    /**
     * Delete ManageCategoryImage
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirectFactory = $this->resultRedirectFactory->create();
        try {
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model = $this->manageCategoryImageFactory->create()->load($id);
                if ($model->getId()) {
                    $model->delete();
                    $this->messageManager->addSuccessMessage(__("Record Delete Successfully."));
                } else {
                    $this->messageManager->addErrorMessage(__("Something went wrong, Please try again."));
                }
            } else {
                $this->messageManager->addErrorMessage(__("Something went wrong, Please try again."));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e, __("We can't delete record, Please try again."));
        }
        return $resultRedirectFactory->setPath('*/*/index');
    }

    /**
     * Check Permission.
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Logicrays_ManageCategoryImage::ManageCategoryImage_delete');
    }
}
