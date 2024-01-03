<?php
declare(strict_types=1);

namespace Logicrays\ManageStudent\Api\Data;

interface ManageStudentSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Email list.
     * @return \Logicrays\ManageStudent\Api\Data\ManageStudentInterface[]
     */
    public function getItems();

    /**
     * Set Email list.
     * @param \Logicrays\ManageStudent\Api\Data\ManageStudentInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return $this
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param int $count
     * @return $this
     */
    public function setTotalCount($count);
}

