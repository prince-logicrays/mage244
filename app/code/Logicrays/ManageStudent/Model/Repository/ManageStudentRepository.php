<?php
namespace Logicrays\ManageStudent\Model\Repository;

use Logicrays\ManageStudent\Api\Data\ManageStudentInterface;
use Logicrays\ManageStudent\Api\ManageStudentRepositoryInterface;
use Magento\Framework\Exception\ALogicrayseadyExistsException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

use Logicrays\ManageStudent\Model\ResourceModel\ManageStudent as ResourceModel;
use Logicrays\ManageStudent\Model\ManageStudentFactory as ModelFactory;
use Logicrays\ManageStudent\Model\ResourceModel\ManageStudent\CollectionFactory as CollectionFactory;
use Logicrays\ManageStudent\Api\Data\ManageStudentSearchResultsInterfaceFactory as SearchResultsFactory;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;

class ManageStudentRepository implements ManageStudentRepositoryInterface
{
    private $resourceModel;
    private $modelFactory;
    private $collectionFactory;
    private $searchResultsFactory;
    protected $_searchCriteriaBuilder;
    protected $_filterBuilder;
    protected $_collectionProcessor;

    /**
     * Email Repository Constructor
     *
     * @param ModelFactory $modelFactory
     * @param ResourceModel $resourceModel
     * @param CollectionFactory $collectionFactory
     * @param SearchResultsFactory $searchResultsFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ModelFactory $modelFactory,
        ResourceModel $resourceModel,
        CollectionFactory $collectionFactory,
        SearchResultsFactory $searchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resourceModel = $resourceModel;
        $this->modelFactory = $modelFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_filterBuilder = $filterBuilder;
        $this->_collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(\Logicrays\ManageStudent\Api\Data\ManageStudentInterface $email)
    {
        try {
            $this->resourceModel->save($email);
        } catch (ALogicrayseadyExistsException $e) {
            throw $e;
        } catch (\Exception $originalException) {
            throw new CouldNotSaveException(__('Unable to save Additional Email'), $originalException);
        }
        return $email;
    }

    /**
     * @inheritDoc
     *
     */
    public function getById($id) {
        return $this->modelFactory->create()->load($id);
    }

    /**
     * @inheritDoc
     */
    public function delete(\Logicrays\ManageStudent\Api\Data\ManageStudentInterface $email)
    {
        if($email->getId()) {
            try {
                $this->deleteById($email->getId());
            } catch (\Exception $originalException) {
                throw new CouldNotDeleteException(__('Unable to delete Student'), $originalException);
            }
        }
        return $this;
    }


    /**
     * @inheritDoc
     */
    public function deleteById($id)
    {
        $model = $this->modelFactory->create()->load($id);
        if($model->getId()) {
            try {
                $this->resourceModel->delete($model);
            } catch (\Exception $originalException) {
                throw new CouldNotDeleteException(__('Unable to delete Student'), $originalException);
            }
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getModelByEmail($customerId, $email)
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('customer_id', $customerId);
        $collection->addFieldToFilter('email', $email);
        

        $model = $collection->getFirstItem();
        if(!$model->getId()) {
            $model = $this->modelFactory->create();
            $model->setCustomerId($customerId);
            $model->setEmail($email);
        } else {
            throw new NoSuchEntityException((__('Student requested did not exist')));
        }
        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();
        $this->_collectionProcessor->process($searchCriteria, $collection);
        $collection->load();
 
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
 
        return $searchResults;
    }
    
    /**
     * @inheritDoc
     */
    public function getListByCustomerId($customerId)
    {
        $filter = $this->_filterBuilder
            ->setField(ManageStudentInterface::CUSTOMER_ID)
            ->setConditionType('eq')
            ->setValue($customerId)
            ->create();
        $searchCriteria = $this->_searchCriteriaBuilder->addFilters([$filter])->create();
        return $this->getList($searchCriteria);
    }
}
