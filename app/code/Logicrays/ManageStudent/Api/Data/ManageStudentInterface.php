<?php
namespace Logicrays\ManageStudent\Api\Data;

interface ManageStudentInterface
{
    const ID = 'id';
    const CUSTOMER_ID = 'customer_id';
    const EMAIL = 'email';
    const F_NAME = 'f_name';
    const L_NAME = 'l_name';
    const GRADE = 'grade';
    const STATUS = 'status';
    
    /**
     * Get Entity Id
     *
     * @return int
     */
    public function getId();

    /**
     * Set Entity Id
     *
     * @param int $id
     * @return void
     */
    public function setId($id);

    /**
     * Get Customer Id
     *
     * @return int
     */

    public function getCustomerId();

    /**
     * Set Customer Id
     *
     * @param int $customerId
     * @return void
     */
    public function setCustomerId($customerId);

    /**
     * Get Email
     *
     * @return string
     */
    public function getEmail();

    /**
     * Set Email 
     *
     * @param string $value
     * @return void
     */
    public function setEmail($value);

      /**
     * Get First Name
     *
     * @return string
     */
    public function getFirstName();

    /**
     * Set First Name
     *
     * @param string $value
     * @return void
     */
    public function setFirstName($value);

    /**
     * Get Last Name
     *
     * @return string
     */
    public function getLastName();

    /**
     * Set Last Name 
     *
     * @param string $value
     * @return void
     */
    public function setLastName($value);

    /**
     * Get Grade
     *
     * @return int
     */
    public function getGrade();

    /**
     * Set Grade
     *
     * @param int $value
     * @return void
     */
    public function setGrade($value);

    /** 
     * Get Status
     * 
     * @return int
     */
    public function getStatus();

    /**
     * Set Status
     *
     * @param int $value
     * @return void
     */
    public function setStatus($value);
}
