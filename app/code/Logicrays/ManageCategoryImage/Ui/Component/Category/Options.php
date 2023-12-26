<?php
namespace Logicrays\ManageCategoryImage\Ui\Component\Category;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;
use Logicrays\ManageCategoryImage\Model\ManageCategoryImageFactory;

class Options implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var ManageCategoryImageFactory
     */
    public $manageCategoryImageFactory;

    /**
     * __construct function
     *
     * @param CollectionFactory $collectionFactory
     * @param ManageCategoryImageFactory $manageCategoryImageFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        ManageCategoryImageFactory $manageCategoryImageFactory,
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->manageCategoryImageFactory = $manageCategoryImageFactory;
    }

    /**
     * ToOptionArray function
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '-- Please Select --', 'value' => ''];
        $collection = $this->collectionFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('is_active', '1')
            ->addAttributeToFilter('level', '2');

        foreach ($collection as $category) {
            $isDisabled = $this->checkOptionShouldBeDisabled($category->getId());
            if (!$isDisabled) {
                $options[] = [
                    'label' => $category->getName(),
                    'value' => $category->getId()
                ];
            }
        }

        return $options;
    }

    /**
     * CheckOptionShouldBeDisabled function
     *
     * @param int $categoryId
     * @return bool
     */
    public function checkOptionShouldBeDisabled($categoryId)
    {
        $cateImageCollection = $this->manageCategoryImageFactory->create()->getCollection();
        $cateImageCollection->addFieldToFilter('category_id', $categoryId);
        if ($cateImageCollection->getData()) {
            return true;
        }
        return false;
    }
}
