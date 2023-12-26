<?php

namespace LR\Supplier\Model\Source;

use LR\Supplier\Model\ResourceModel\SupplierItem\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class SupplierName implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    public function toOptionArray()
    {
        $options[] = ['label' => '-- Please Select --', 'value' => ''];
        $collection = $this->collectionFactory->create();

        foreach ($collection as $supplierData) {
            $options[] = [
                'label' => $supplierData->getName(),
                'value' => $supplierData->getEntityId(),
            ];
        }

        return $options;
    }
}
