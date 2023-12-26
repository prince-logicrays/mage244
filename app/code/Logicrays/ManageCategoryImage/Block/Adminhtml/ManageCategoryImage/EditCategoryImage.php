<?php
namespace Logicrays\ManageCategoryImage\Block\Adminhtml\ManageCategoryImage;

use Magento\Framework\UrlInterface;

class EditCategoryImage extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Logicrays\ManageCategoryImage\Model\ManageCategoryImageFactory
     */
    protected $manageCategoryImageFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected $attributeCollectionFactory;

    /**
     * @var \Magento\Backend\Model\Url
     */
    protected $url;

    /**
     * @var \Magento\Catalog\Api\ProductAttributeRepositoryInterface
     */
    protected $productAttributeRepository;

    /**
     * @var \Magento\Catalog\Api\ProductAttributeOptionManagementInterface
     */
    protected $productAttributeOptionManagement;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * __construct function
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Logicrays\ManageCategoryImage\Model\ManageCategoryImageFactory $manageCategoryImageFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory
     * @param \Magento\Backend\Model\Url $url
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $productAttributeRepository
     * @param \Magento\Catalog\Api\ProductAttributeOptionManagementInterface $productAttributeOptionManagement
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Logicrays\ManageCategoryImage\Model\ManageCategoryImageFactory $manageCategoryImageFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\Backend\Model\Url $url,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $productAttributeRepository,
        \Magento\Catalog\Api\ProductAttributeOptionManagementInterface $productAttributeOptionManagement,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->manageCategoryImageFactory = $manageCategoryImageFactory;
        $this->collectionFactory = $collectionFactory;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->url = $url;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->productAttributeOptionManagement = $productAttributeOptionManagement;
        $this->formKey = $formKey;
        $this->resourceConnection = $resourceConnection;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * GetCurrentEditData function
     *
     * @return array
     */
    public function getCurrentEditData()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->manageCategoryImageFactory->create()->load($id);
        return $model;
    }

    /**
     * GetCurrentEditId function
     *
     * @return array
     */
    public function getCurrentEditId()
    {
        return $this->getRequest()->getParam('id');
    }

    /**
     * SetSelectedCategory function
     *
     * @return int
     */
    public function setSelectedCategory()
    {
        $data = $this->getCurrentEditData();
        return $data->getCategoryId();
    }

    /**
     * SetSelectedAttributes function
     *
     * @return array
     */
    public function setSelectedAttributes()
    {
        $data = $this->getCurrentEditData();
        $attributes =  $data->getAttributes();
        return explode(',', $attributes);
    }

    /**
     * GetCurrentCategoryData function
     *
     * @return array
     */
    public function getCurrentCategoryData()
    {
        $tableName = $this->resourceConnection->getTableName('logicrays_category_attribute_option_image');
        $connection = $this->resourceConnection->getConnection();
        $cateId = $this->setSelectedCategory();

        $select = $connection->select()
            ->from(
                ['c' => $tableName],
                ['*']
            )
            ->where(
                "c.category_id = :category_id"
            );
        $bind = ['category_id'=>$cateId];
        return $connection->fetchAll($select, $bind);
    }

    /**
     * GetMediaUrl function
     *
     * @return string
     */
    public function getMediaUrl()
    {
        $mediaUrl = $this->storeManager-> getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl.'category_images';
    }
    /**
     * GetAllParentCategories function
     *
     * @return array
     */
    public function getAllParentCategories()
    {
        $options = [];
        $collection = $this->collectionFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('is_active', '1')
            ->addAttributeToFilter('level', '2');

        foreach ($collection as $category) {
            $options[] = [
                'label' => $category->getName(),
                'value' => $category->getId(),
            ];
        }
        return $options;
    }

    /**
     * GetAllAttributes function
     *
     * @return array
     */
    public function getAllAttributes()
    {
        $attributeInfo = $this->attributeCollectionFactory->create()
            ->addFieldToFilter('is_filterable', '1')
            ->addFieldToFilter('is_visible_on_front', '0')
            ->addFieldToFilter('frontend_input', 'select');

        $attribute_data = [];
        foreach ($attributeInfo as $items) {
            $attribute_data[] =[
                'label' => $items->getFrontendLabel(),
                'value' => $items->getAttributeCode(),
            ];
        }
        return $attribute_data;
    }

    /**
     * GetAttributeOptionByCode function
     *
     * @param string $attributeCode
     * @return array
     */
    public function getAttributeOptionByCode($attributeCode)
    {
        $attribute = $this->productAttributeRepository->get($attributeCode);
        $options = $this->productAttributeOptionManagement->getItems($attribute->getAttributeId());

        $optionValues = [];
        foreach ($options as $option) {
            $optionValues[] =[
                'label' => $option->getLabel(),
                'value' => $option->getValue(),
            ];
        }
        return $optionValues;
    }

    /**
     * Get FormAction function
     *
     * @return string
     */
    public function getFormAction()
    {
        // return $this->url->getUrl('managecategoryimage/managecategoryimage/index');
        return $this->url->getUrl('managecategoryimage/managecategoryimage/save');
    }

    /**
     * Get form key
     *
     * @return string
     */
    public function getFormKey()
    {
         return $this->formKey->getFormKey();
    }

    /**
     * GetBackendUrl function
     *
     * @return string
     */
    public function getBackeUrl()
    {
        return $this->url->getUrl('managecategoryimage/managecategoryimage/index');
    }

    /**
     * GetDeleteUrl function
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->url->getUrl('managecategoryimage/managecategoryimage/delete/id/'.$this->getCurrentEditId());
    }
}
