<?php
declare(strict_types=1);

namespace Logicrays\ManageStudent\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ManageStudentRepositoryInterface
{

    /**
     * Save Email
     * @param \Logicrays\ManageStudent\Api\Data\ManageStudentInterface $student
     * @return \Logicrays\ManageStudent\Api\Data\ManageStudentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Logicrays\ManageStudent\Api\Data\ManageStudentInterface $student
    );

    /**
     * Retrieve Email
     * @param string $id
     * @return \Logicrays\ManageStudent\Api\Data\ManageStudentInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);

    /**
     * Delete Email
     * @param \Logicrays\ManageStudent\Api\Data\ManageStudentInterface $student
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Logicrays\ManageStudent\Api\Data\ManageStudentInterface $student
    );

    /**
     * Delete Email by ID
     * @param string $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);

    /**
     * Get Email using customer id and email address
     *
     * @param int $customerId
     * @param string $email
     * @return \Logicrays\ManageStudent\Api\Data\ManageStudentInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getModelByEmail($customerId, $student);

    /**
     * Retrieve Emails matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Logicrays\ManageStudent\Api\Data\ManageStudentSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Retrieve additional emails list by customer id
     *
     * @param int $customerId
     * @return \Logicrays\ManageStudent\Api\Data\ManageStudentSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListByCustomerId($customerId);
}

