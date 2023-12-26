<?php
namespace Logicrays\ManageCategoryImage\Ui\Component\Category;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class EditOptions implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * __construct function
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory,
    ) {
        $this->collectionFactory = $collectionFactory;
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
            $options[] = [
                'label' => $category->getName(),
                'value' => $category->getId(),
                'disabled' => '100',
            ];
        }

        return $options;
    }
}
