<?php
namespace Logicrays\ManageStudent\Model;

use Magento\Framework\Model\AbstractModel;
use Logicrays\ManageStudent\Model\ResourceModel\ManageStudent as ResourceModel;

class ManageStudent extends AbstractModel implements \Logicrays\ManageStudent\Api\Data\ManageStudentInterface
{
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * Retrieve block id
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @inheritDoc
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId() : int
    {
        return (int)$this->getData('customer_id');
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId($customerId)
    {
        return $this->setData('customer_id', $customerId);
    }

    /**
     * @inheritDoc
     */
    public function getEmail() : string 
    {
        return (string)$this->getData('email');
    }

    /**
     * @inheritDoc
     */
    public function setEmail($email) {
        return $this->setData("email", $email);
    }

    /**
     * @inheritDoc
     */
    public function getFirstName() : string 
    {
        return (string)$this->getData('first_name');
    }

    /**
     * @inheritDoc
     */
    public function setFirstName($value) {
        return $this->setData("first_name", $value);
    }

    /**
     * @inheritDoc
     */
    public function getLastName() : string 
    {
        return (string)$this->getData('last_name');
    }

    /**
     * @inheritDoc
     */
    public function setLastName($value) {
        return $this->setData("last_name", $value);
    }
    /**
     * @inheritDoc
     */
    public function getGrade() : int 
    {
        return (int)$this->getData('grade');
    }

    /**
     * @inheritDoc
     */
    public function setGrade($value) {
        return $this->setData("grade", $value);
    }

    /**
     * @inheritDoc
     */
    public function getStatus() : int 
    {
        return (int)$this->getData('status');
    }

    /**
     * @inheritDoc
     */
    public function setStatus($value) {
        return $this->setData("status", $value);
    }
}