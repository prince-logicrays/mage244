<?php

namespace LR\Supplier\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use LR\Supplier\Model\Supplier;
use LR\Supplier\Model\SupplierItem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\File\Csv;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\UrlInterface;

class Save extends Action
{
    /**
     * @var Supplier
     */
    protected $supplierModel;

    /**
     * @var SupplierItem
     */
    protected $supplierItemModel;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var Csv
     */
    protected $csv;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Action\Context $context
     * @param Supplier $supplierModel
     * @param SupplierItem $supplierItemModel
     * @param DirectoryList $directoryList
     * @param File $file
     * @param Csv $csv
     * @param LoggerInterface $logger
     */
    public function __construct(
        Action\Context $context,
        Supplier $supplierModel,
        SupplierItem $supplierItemModel,
        DirectoryList $directoryList,
        File $file,
        Csv $csv,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->directoryList = $directoryList;
        $this->supplierModel = $supplierModel;
        $this->supplierItemModel = $supplierItemModel;
        $this->file = $file;
        $this->csv = $csv;
        $this->logger = $logger;
    }

    /**
     * Save supplier item record action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $supplierName = $this->supplierItemModel->load($data['name']);
        $csvFileName = $data['file']['0']['file'];
        $rootDirectory = $this->directoryList->getRoot();
        $csvPath = $rootDirectory . '/pub/media/supplier_import_csv/' . $csvFileName;
        try {
            if ($this->file->isExists($csvPath)) {
                //set delimiter, for tab pass "\t"
                $this->csv->setDelimiter(",");
                //get data as an array
                $data = $this->csv->getData($csvPath);
                if (!empty($data)) {
                    $tableColumns = [
                        'sku',
                        'product_name',
                        'description',
                        'category_main',
                        'category_sub',
                        'pricing',
                        'printing',
                        'colours',
                        'images',
                        'specification',
                        'packaging',
                        'image360',
                        'certification',
                        'delivery_time',
                        'origin',
                        'keywords'
                    ];
                    foreach ($data as $key => $value) {
                        if ($key === 0) {
                            $header = $value;
                        } else {
                            if ($tableColumns == $header) {
                                $csvData = array_combine($header, $value);
                                $csvData['supplier_id'] = $supplierName->getId();
                                $csvData['supplier'] = $supplierName->getName();
                                $this->supplierModel->setData($csvData);
                                $this->supplierModel->save();
                            } else {
                                $this->messageManager->addError(__('Column Missing.'));
                            }
                        }
                    }
                    $this->messageManager->addSuccess(__('The data has been saved.'));
                    $resultRedirect = $this->resultRedirectFactory->create();
                    return $resultRedirect->setPath('*/grid/index');
                }
            } else {
                $this->logger->info('Csv file not exist');
                return __('Csv file not exist');
            }
        } catch (FileSystemException $e) {
            $this->logger->info($e->getMessage());
        }
    }
}
