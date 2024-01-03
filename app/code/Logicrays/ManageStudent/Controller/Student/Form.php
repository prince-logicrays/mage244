<?php
namespace Logicrays\ManageStudent\Controller\Student;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

class Form extends \Logicrays\ManageStudent\Controller\ManageStudent implements HttpGetActionInterface
{
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('managestudent/student');
        }
        return $resultPage;
    }
}
