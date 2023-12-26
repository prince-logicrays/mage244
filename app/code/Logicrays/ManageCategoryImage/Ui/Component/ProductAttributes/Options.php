<?php
namespace Logicrays\ManageCategoryImage\Ui\Component\ProductAttributes;

use \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class Options implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected $attributeFactory;

    /**
     * __construct function
     *
     * @param CollectionFactory $attributeFactory
     */
    public function __construct(
        CollectionFactory $attributeFactory
    ) {
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * ToOptionArray function
     *
     * @return array
     */
    public function toOptionArray()
    {
        $attributeInfo = $this->attributeFactory->create()
            ->addFieldToFilter('is_filterable', '1')
            ->addFieldToFilter('is_visible_on_front', '0')
            ->addFieldToFilter('frontend_input', 'select');

        foreach ($attributeInfo as $items) {
            $attribute_data[] =[
                'label' => $items->getFrontendLabel(),
                'value' => $items->getAttributeCode(),
            ];
        }
        return $attribute_data;
    }
}
