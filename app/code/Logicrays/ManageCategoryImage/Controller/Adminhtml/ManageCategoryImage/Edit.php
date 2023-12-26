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

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;

class Edit extends Action implements HttpGetActionInterface
{
    /**
     * ResultPageFactory variable
     *
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Logicrays\ManageCategoryImage\Model\ManageCategoryImageFactory
     */
    private $manageCategoryImageFactory;

    /**
     * __construct function
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Logicrays\ManageCategoryImage\Model\ManageCategoryImageFactory $manageCategoryImageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Logicrays\ManageCategoryImage\Model\ManageCategoryImageFactory $manageCategoryImageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->manageCategoryImageFactory = $manageCategoryImageFactory;
        parent::__construct($context);
    }

    /**
     * ManageCategoryImage edit form
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultPage = $this->resultPageFactory->create();
        $id = $this->getRequest()->getParam('id');

        $model = $this->manageCategoryImageFactory->create()->load($id);
        if ($model->getId()) {
            $resultPage->getConfig()->getTitle()->set(__('Edit Category Image #'.$id));
            return $resultPage;
        }
        return $resultRedirect->setPath('*/*/index');
    }

    /**
     * Check Permission.
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Logicrays_ManageCategoryImage::managecategoryimage_edit');
    }
}
