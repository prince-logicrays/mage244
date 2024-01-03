<?php
namespace Logicrays\ManageStudent\Controller\Student;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
class Index extends \Logicrays\ManageStudent\Controller\ManageStudent implements HttpGetActionInterface
{
    public function execute()
    {
        $emails = $this->_managestudentRepository->getListByCustomerId($this->_getSession()->getCustomerId());
        if ($emails->getTotalCount()) {
            /** @var \Magento\Framework\View\Result\Page $resultPage */
            $resultPage = $this->resultPageFactory->create();
            $block = $resultPage->getLayout()->getBlock('managestudent');
            if ($block) {
                $block->setRefererUrl($this->_redirect->getRefererUrl());
            }
            return $resultPage;
        } else {
            return $this->resultRedirectFactory->create()->setPath('*/*/new');
        }
    }
}
