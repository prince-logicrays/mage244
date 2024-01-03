<?php
namespace Logicrays\ManageStudent\Controller\Student;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;

class Delete extends \Logicrays\ManageStudent\Controller\ManageStudent implements HttpPostActionInterface, HttpGetActionInterface
{
    public function execute()
    {
        $studentId = $this->getRequest()->getParam('id', false);

        if ($studentId && $this->_formKeyValidator->validate($this->getRequest())) {
            try {
                $student = $this->_managestudentRepository->getById($studentId);
                if ($student->getCustomerId() == $this->_getSession()->getCustomerId()) {
                    $this->_managestudentRepository->delete($student);
                    $this->messageManager->addSuccess(__('You deleted the student.'));
                } else {
                    $this->messageManager->addError(__('We can\'t delete the student right now.'));
                }
            } catch (\Exception $other) {
                $this->messageManager->addException($other, __('We can\'t delete the student right now.'));
            }
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }
}
