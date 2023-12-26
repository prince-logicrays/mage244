<?php

namespace LR\Supplier\Controller\Adminhtml\Import;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Exception;

class File extends Action
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Context $context
     * @param UploaderFactory $uploaderFactory
     * @param Filesystem $filesystem
     * @param StoreManagerInterface $storeManager
     * @param Filesystem $fileSystem
     */
    public function __construct(
        Context $context,
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        Filesystem $fileSystem
    ) {
        parent::__construct($context);
        $this->uploaderFactory = $uploaderFactory;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->storeManager = $storeManager;
        $this->fileSystem = $fileSystem;
    }

    /**
     * Csv upload
     *
     * @return void
     */
    public function execute()
    {
        $jsonResult = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            $fileUploader = $this->uploaderFactory->create([
                'fileId' => 'file'
            ]);
            $fileUploader->setAllowedExtensions(['csv']);
            $fileUploader->setAllowRenameFiles(true);
            $fileUploader->setAllowCreateFolders(true);
            $fileUploader->setFilesDispersion(false);

            $data = $fileUploader->save($this->mediaDirectory->getAbsolutePath('supplier_import_csv'));

            return $jsonResult->setData($data);

        } catch (LocalizedException|Exception $error) {
            return $jsonResult->setData(['errorcode' => 0, 'error' => $error->getMessage()]);
        }
    }
}
