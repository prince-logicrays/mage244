<?php
namespace Logicrays\ManageStudent\Controller\Student;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Customer\Model\Metadata\FormFactory;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Exception\InputException;

use Logicrays\ManageStudent\Model\ManageStudentFactory;
use Logicrays\ManageStudent\Model\Repository\ManageStudentRepository;
use Logicrays\ManageStudent\Helper\Data as Helper;

class FormPost extends \Logicrays\ManageStudent\Controller\ManageStudent implements HttpPostActionInterface
{
    protected function _extractStudent()
    {
        $formStudentData = $this->getformStudentData();
        $studentData = array();
        
        $studentDataObject = $this->_managestudentFactory->create();
        if ($studentId = $this->getRequest()->getParam('id')) {
            
            $studentDataObject->load($studentId);
            if($studentDataObject->getCustomerId() != $this->_getSession()->getCustomerId()) {
                throw new \Exception();
            }
        } else {
            $studentDataObject->setCustomerId($this->_getSession()->getCustomerId());
        }

        $this->dataObjectHelper->populateWithArray(
            $studentDataObject,
            $formStudentData,
            \Logicrays\ManageStudent\Api\Data\ManageStudentInterface::class
        );
        

        return $studentDataObject;
    }

    protected function getformStudentData() {
        $formStudentData = [];
        $postData = $this->getRequest()->getPost();
        if($postData) {
            $formStudentData["email"] = $postData["email"];
            $formStudentData["first_name"] = $postData["first_name"];
            $formStudentData["last_name"] = $postData["last_name"];
            $formStudentData["grade"] = $postData["grade"];
            $formStudentData["status"] = (isset($postData["status"])) ? 1 : 0;
        }
        return $formStudentData;
    }

    public function execute()
    {
        $redirectUrl = null;
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        if (!$this->getRequest()->isPost()) {
            $this->_getSession()->setStudentFormData($this->getRequest()->getPostValue());
            return $this->resultRedirectFactory->create()->setUrl(
                $this->_redirect->error($this->_buildUrl('*/*/edit'))
            );
        }

        try {
            $email = $this->_extractStudent();
            $this->_managestudentRepository->save($email);
            $this->messageManager->addSuccessMessage(__('You saved the new student.'));
            $url = $this->_buildUrl('*/*/index', ['_secure' => true]);
            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->success($url));
        } catch (InputException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            foreach ($e->getErrors() as $error) {
                $this->messageManager->addErrorMessage($error->getMessage());
            }
        } catch (\Exception $e) {
            $redirectUrl = $this->_buildUrl('*/*/index');
            $this->messageManager->addExceptionMessage($e, __('We can\'t save the new student.'));
        }

        $url = $redirectUrl;
        if (!$redirectUrl) {
            $this->_getSession()->setEmailFormData($this->getRequest()->getPostValue());
            $url = $this->_buildUrl('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }

        return $this->resultRedirectFactory->create()->setUrl($this->_redirect->error($url));
    }
}
