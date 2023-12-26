<?php
namespace Logicrays\ManageCategoryImage\Controller\Adminhtml\ManageCategoryImage;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends Action implements HttpPostActionInterface
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;

    /**
     * @var \Logicrays\ManageCategoryImage\Model\ManageCategoryImageFactory
     */
    protected $manageCategoryImageFactory;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;

    /**
     * @var \Logicrays\ManageCategoryImage\Model\CategoryOptionImageFactory
     */
    protected $categoryOptionImageFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    /**
     * __construct function
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Logicrays\ManageCategoryImage\Model\ManageCategoryImageFactory $manageCategoryImageFactory
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Logicrays\ManageCategoryImage\Model\CategoryOptionImageFactory $categoryOptionImageFactory
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Logicrays\ManageCategoryImage\Model\ManageCategoryImageFactory $manageCategoryImageFactory,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Logicrays\ManageCategoryImage\Model\CategoryOptionImageFactory $categoryOptionImageFactory,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->_pageFactory = $pageFactory;
        $this->manageCategoryImageFactory = $manageCategoryImageFactory;
        $this->formKeyValidator = $formKeyValidator;
        $this->categoryOptionImageFactory = $categoryOptionImageFactory;
        $this->json = $json;
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->resourceConnection = $resourceConnection;
        return parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPageFactory = $this->resultRedirectFactory->create();
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $this->messageManager->addErrorMessage(__("Form key is Invalidate"));
            return $resultPageFactory->setPath('*/*/index');
        }
        $data = $this->getRequest()->getParams();

        // echo '<pre>';
        // print_r($data);
        // die;

        $atributes = implode(',', $data['attributes']);
        $saveData = [
            'category_id' => $data['category_id'],
            'attributes' => $atributes,
        ];

        $msg = 'Saved';
        // make condition for updates
        if (array_key_exists("id", $data)) {
            $this->setReferenceTableData();
            $this->updateReferenceTableData();

            $saveData = [
                'category_id' => $data['category_id'],
                'attributes' => $atributes,
                'id' => $data['id']
            ];
            $msg = 'Updated';
        }

        try {
            if ($saveData) {
                $model = $this->manageCategoryImageFactory->create();
                $model->setData($saveData)->save();
                $id = $model->getId();
            }
            $this->messageManager->addSuccessMessage(__("Category Image $msg Successfully."));
            return $resultPageFactory->setPath('*/*/edit', ['id' => $id]);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e, __("We can't submit your request, Please try again."));
        }
    }

    /**
     * Set Reference TableData function
     *
     * @return array
     */
    public function setReferenceTableData()
    {
        $data = $this->getRequest()->getParams();

        $imagesToSave = $this->newImageUpload();

        $newSeparatedRowValues = $this->newSeparatedRowValues();

        try {
            $a1 = 0;
            foreach ($newSeparatedRowValues as $separatedRow) {
                foreach ($imagesToSave as $key => $image) {
                    $imageKey = $a1.'_image';
                    if (isset($image[$imageKey])) {
                        $imagePath = $image[$imageKey];
                    }
                }
                $jsonAttributeOptions = $this->json->serialize($separatedRow);

                $saveReferenceTableData = [
                    'category_id' => $data['category_id'],
                    'attribute_options' => $jsonAttributeOptions,
                    'image' => $imagePath
                ];

                if ($saveReferenceTableData) {
                    $model = $this->categoryOptionImageFactory->create();
                    $model->setData($saveReferenceTableData)->save();
                }
                $a1++;
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e, __("We can't submit your request, Please try again."));
        }
    }

    /**
     * New Separated RowValues function
     *
     * @return array
     */
    public function newSeparatedRowValues()
    {
        $data = $this->getRequest()->getParams();

        $attributeOptions = [];
        foreach ($data as $key => $value) {
            if (strpos($key, 'attributeoption_option') === 0 && is_array($value)) {
                $optionName = str_replace('attributeoption_option_', '', $key);
                foreach ($value as $index => $item) {
                    $attributeOptions[$index . '_' . $optionName] = $item;
                }
            }
        }

        $separatedRowValues = [];
        foreach ($attributeOptions as $key => $value) {
            $index = explode('_', $key);
            if (isset($separatedRowValues[$index[1]])) {
                $separatedRowValues[$index[1]][$index[2]] = $value;
            } else {
                $separatedRowValues[$index[1]] = [$index[2] => $value];
            }
        }
        return $separatedRowValues;
    }

    /**
     * New ImageUpload function
     *
     * @return array
     */
    public function newImageUpload()
    {
        $imageFile = $this->getRequest()->getFiles();

        try {
            $imageToSave = [];
            foreach ($imageFile as $key => $image) {
                $checkNewRecord = explode('_', $key);
                if ($checkNewRecord[0] != 'edit') {
                    $uploaderFactory = $this->uploaderFactory->create(['fileId' => $image]);
                    $uploaderFactory->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                    $uploaderFactory->setAllowRenameFiles(true);
                    $uploaderFactory->setFilesDispersion(true);
                    $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                    $destinationPath = $mediaDirectory->getAbsolutePath('category_images/');
                    $result = $uploaderFactory->save($destinationPath);

                    if (!$result) {
                        throw new LocalizedException(
                            __('File cannot be saved to path: $1', $destinationPath)
                        );
                    }
                    $imageKeys = explode('_', $key);
                    $imageRowId = $imageKeys[2].'_'.$imageKeys[3];
                    $imageToSave[] = [
                        $imageRowId => $result['file']
                    ];
                }
            }
            return $imageToSave;
        } catch (\Exception $e) {
            $this->messageManager->addError(__("Image not Upload Pleae Try Again"));
        }
    }

    /**
     * Update Reference TableData function
     *
     * @return mixed
     */
    public function updateReferenceTableData()
    {
        $updateSeparatedRowValues = $this->updateSeparatedRowValues();
        // echo '<pre>';
        // print_r($updateSeparatedRowValues);
        $updateImageUpload = $this->updateImageUpload();
        // print_r($updateImageUpload);

        $tableName = $this->resourceConnection->getTableName('logicrays_category_attribute_option_image');
        $connection = $this->resourceConnection->getConnection();

        foreach ($updateSeparatedRowValues as $id => $updatedValue) {
            $updatedImagePath = '';
            foreach ($updateImageUpload as $key => $image) {
                $imageKey = $id.'_image';
                if (isset($image[$imageKey])) {
                    $updatedImagePath = $image[$imageKey];
                }
            }

            $jsonUpdatedValue = $this->json->serialize($updatedValue);

            $select = $connection->select()
                ->from(
                    ['c' => $tableName],
                    ['*']
                )
                ->where(
                    "c.id = :id"
                );
            $bind = ['id'=>$id];
            $currentData = $connection->fetchAll($select, $bind);

            // echo '<pre>';
            // print_r($currentData);
            if (!$updatedImagePath) {
                $updatedImagePath = $currentData[0]['image'];
            }

            $dataToUpdate = [
                'id' => $id,
                'category_id' => $currentData[0]['category_id'],
                'attribute_options' => $jsonUpdatedValue,
                'image' => $updatedImagePath,
            ];

            // print_r($dataToUpdate);
            $model = $this->manageCategoryImageFactory->create();
            $model->setData($dataToUpdate)->save();
            // echo '<br>';
        }
        // die;
    }

    /**
     * Update ImageUpload function
     *
     * @return array
     */
    public function updateImageUpload()
    {
        $imageFile = $this->getRequest()->getFiles();

        try {
            $imageToSave = [];
            foreach ($imageFile as $key => $image) {
                $checkNewRecord = explode('_', $key);
                if ($checkNewRecord[0] == 'edit' && (!empty($image['full_path']))) {
                    $uploaderFactory = $this->uploaderFactory->create(['fileId' => $image]);
                    $uploaderFactory->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                    $uploaderFactory->setAllowRenameFiles(true);
                    $uploaderFactory->setFilesDispersion(true);
                    $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                    $destinationPath = $mediaDirectory->getAbsolutePath('category_images/');
                    $result = $uploaderFactory->save($destinationPath);

                    if (!$result) {
                        throw new LocalizedException(
                            __('File cannot be saved to path: $1', $destinationPath)
                        );
                    }
                    $imageKeys = explode('_', $key);
                    $imageRowId = $imageKeys[3].'_'.$imageKeys[4];
                    $imageToSave[] = [
                        $imageRowId => $result['file']
                    ];
                }
            }

            return $imageToSave;
        } catch (\Exception $e) {
            $this->messageManager->addError(__("Image not Upload Pleae Try Again"));
        }
    }

    /**
     * Update Separated RowValues function
     *
     * @return array
     */
    public function updateSeparatedRowValues()
    {
        $data = $this->getRequest()->getParams();

        $attributeOptions = [];
        foreach ($data as $key => $value) {
            if (strpos($key, 'edit_attributeoption_option') === 0 && is_array($value)) {
                $optionName = str_replace('edit_attributeoption_option_', '', $key);
                foreach ($value as $index => $item) {
                    $attributeOptions[$index . '_' . $optionName] = $item;
                }
            }
        }

        $separatedRowValues = [];
        foreach ($attributeOptions as $key => $value) {
            $index = explode('_', $key);
            if (isset($separatedRowValues[$index[1]])) {
                $separatedRowValues[$index[1]][$index[2]] = $value;
            } else {
                $separatedRowValues[$index[1]] = [$index[2] => $value];
            }
        }
        return $separatedRowValues;
    }

    /**
     * Is the user allowed to view the page.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Logicrays_ManageCategoryImage::managecategoryimage_save');
    }
}
