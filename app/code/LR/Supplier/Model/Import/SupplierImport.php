<?php

namespace LR\Supplier\Model\Import;

use LR\Supplier\Model\Import\SupplierImport\RowValidatorInterface as ValidatorInterface;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\Framework\App\ResourceConnection;

class SupplierImport extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity
{
    // public const ENTITY_ID = 'entity_id';
    public const SKU = 'sku';
    public const PRODUCT_NAME = 'product_name';
    public const DESCRIPTION = 'description';
    public const CATEGORY_MAIN = 'category_main';
    public const CATEGORY_SUB = 'category_sub';
    public const PRICING = 'pricing';
    public const PRINTING = 'printing';
    public const COLOURS = 'colours';
    public const IMAGES = 'images';
    public const SPECIFICATION = 'specification';
    public const PACKAGING = 'packaging';
    public const IMAGE360 = 'image360';
    public const CERTIFICATION = 'certification';
    public const DELIVERY_TIME = 'delivery_time';
    public const ORIGIN = 'origin';
    public const KEYWORDS = 'keywords';

    public const TABLE_Entity = 'lr_supplier_data';
    /** * Validation failure message template definitions * * @var array */
    protected $_messageTemplates = [ValidatorInterface::ERROR_TITLE_IS_EMPTY => 'Name is empty',];

    // protected $_permanentAttributes = [self::ENTITY_ID];
    protected $needColumnCheck = true;
    protected $groupFactory;
    protected $validColumnNames = [
        // self::ENTITY_ID,
        self::SKU,
        self::PRODUCT_NAME,
        self::DESCRIPTION,
        self::CATEGORY_MAIN,
        self::CATEGORY_SUB,
        self::PRICING,
        self::PRINTING,
        self::COLOURS,
        self::IMAGES,
        self::SPECIFICATION,
        self::PACKAGING,
        self::IMAGE360,
        self::CERTIFICATION,
        self::DELIVERY_TIME,
        self::ORIGIN,
        self::KEYWORDS,
    ];
    protected $logInHistory = true;
    protected $_validators = [];
    protected $_connection;
    protected $_resource;

    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Stdlib\StringUtils $string,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Magento\Customer\Model\GroupFactory $groupFactory
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->_resource = $resource;
        $this->_connection = $resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $this->errorAggregator = $errorAggregator;
        $this->groupFactory = $groupFactory;
    }

    public function getValidColumnNames()
    {
        return $this->validColumnNames;
    }

    public function getEntityTypeCode()
    {
        return 'lr_supplier_data';
    }

    public function validateRow(array $rowData, $rowNum)
    {
        if (isset($this->_validatedRows[$rowNum]))
        {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }
        $this->_validatedRows[$rowNum] = true;
        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    protected function _importData()
    {
        $this->saveEntity();
        return true;
    }

    public function saveEntity()
    {
        $this->saveAndReplaceEntity();
        return $this;
    }

    public function replaceEntity()
    {
        $this->saveAndReplaceEntity();
        return $this;
    }

    public function deleteEntity()
    {
        $listTitle = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch())
        {
            foreach ($bunch as $rowNum => $rowData)
            {
                $this->validateRow($rowData, $rowNum);
                if (!$this->getErrorAggregator()->isRowInvalid($rowNum))
                    {
                        $rowTtile = $rowData[self::ID];
                        $listTitle[] = $rowTtile;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated())
                {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                }
            }
        }
        if ($listTitle)
        {
            $this->deleteEntityFinish(array_unique($listTitle),self::TABLE_Entity);
        }
        return $this;
    }

    protected function saveAndReplaceEntity()
    {
        $behavior = $this->getBehavior();
        $listTitle = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch())
        {
            $entityList = [];
            foreach ($bunch as $rowNum => $rowData)
            {
                if (!$this->validateRow($rowData, $rowNum)) {
                    $this->addRowError(ValidatorInterface::ERROR_TITLE_IS_EMPTY, $rowNum);
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }

                $rowTtile = $rowData[self::SKU];
                $listTitle[] = $rowTtile;
                $entityList[$rowTtile][] = [
                // self::ENTITY_ID => $rowData[self::ENTITY_ID],
                self::SKU => $rowData[self::SKU],
                self::PRODUCT_NAME => $rowData[self::PRODUCT_NAME],
                self::DESCRIPTION => $rowData[self::DESCRIPTION],
                self::CATEGORY_MAIN => $rowData[self::CATEGORY_MAIN],
                self::CATEGORY_SUB => $rowData[self::CATEGORY_SUB],
                self::PRICING => $rowData[self::PRICING],
                self::PRINTING => $rowData[self::PRINTING],
                self::COLOURS => $rowData[self::COLOURS],
                self::IMAGES => $rowData[self::IMAGES],
                self::SPECIFICATION => $rowData[self::SPECIFICATION],
                self::PACKAGING => $rowData[self::PACKAGING],
                self::IMAGE360 => $rowData[self::IMAGE360],
                self::CERTIFICATION => $rowData[self::CERTIFICATION],
                self::DELIVERY_TIME => $rowData[self::DELIVERY_TIME],
                self::ORIGIN => $rowData[self::ORIGIN],
                self::KEYWORDS => $rowData[self::KEYWORDS],];
            }
            if (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $behavior) {
                if ($listTitle) {
                    if ($this->deleteEntityFinish(array_unique($listTitle), self::TABLE_Entity)) {
                        $this->saveEntityFinish($entityList, self::TABLE_Entity);
                    }
                }
            } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $behavior) {
                $this->saveEntityFinish($entityList, self::TABLE_Entity);
            }
        }
        return $this;
    }

    protected function saveEntityFinish(array $entityData, $table)
    {
        if ($entityData)
        {
            // echo "<pre>";
            // print_r($entityData);exit;
            $tableName = $this->_connection->getTableName($table);
            $entityIn = [];
            foreach ($entityData as $id => $entityRows) {
                foreach ($entityRows as $row) {
                    $entityIn[] = $row;
                }
            }
            if ($entityIn) {
                $this->_connection->insertOnDuplicate($tableName, $entityIn,[
                // self::ENTITY_ID,
                self::SKU,
                self::PRODUCT_NAME,
                self::DESCRIPTION,
                self::CATEGORY_MAIN,
                self::CATEGORY_SUB,
                self::PRICING,
                self::PRINTING,
                self::COLOURS,
                self::IMAGES,
                self::SPECIFICATION,
                self::PACKAGING,
                self::IMAGE360,
                self::CERTIFICATION,
                self::DELIVERY_TIME,
                self::ORIGIN,
                self::KEYWORDS,]);
            }
        }
        return $this;
    }
 
    protected function deleteEntityFinish(array $ids, $table)
    {
        if ($table && $listTitle) {
            try {
                $this->countItemsDeleted += $this->_connection->delete(
                $this->_connection->getTableName($table),
                $this->_connection->quoteInto('id IN (?)', $ids));
                return true;
            }
            catch (\Exception $e)
            {
                return false;
            }
        } else {
            return false;
        }
    }
}
