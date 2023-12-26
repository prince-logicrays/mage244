<?php

namespace Cellularisrael\BundleProduct\Model\ResourceModel;

class Option extends \Magento\Bundle\Model\ResourceModel\Option
{
    /**
     * Save store description in database
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return void
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $conditions = [
            'option_id = ?' => $object->getId(),
            'store_id = ?' => $object->getStoreId(),
            'parent_product_id = ?' => $object->getParentId()
        ];

        $connection = $this->getConnection();

        if ($this->isOptionPresent($conditions)) {
            $connection->update(
                $this->getTable('catalog_product_bundle_option_value'),
                [
                    'title' => $object->getTitle(),
                    'store_description' => $object->getStoreDescription()
                ],
                $conditions
            );
        } else {
            $data = new \Magento\Framework\DataObject();
            $data->setOptionId($object->getId())
                ->setStoreId($object->getStoreId())
                ->setParentProductId($object->getParentId())
                ->setTitle($object->getTitle())
                ->setStoreDescription($object->getStoreDescription());

            $connection->insert($this->getTable('catalog_product_bundle_option_value'), $data->getData());

            /**
             * Also saving default value if this store view scope
             */
            if ($object->getStoreId()) {
                $mainConditions = [
                    'option_id = ?' => $object->getId(),
                    'store_id = 0' => $object->getStoreId(),
                    'parent_product_id = ?' => $object->getParentId()
                ];
                if (!$this->isOptionPresent($conditions)) {
                    $data->setStoreId(0);
                    $data->setTitle($object->getDefaultTitle());
                    $connection->insert($this->getTable('catalog_product_bundle_option_value'), $data->getData());
                }
            }
        }

        return $this;
    }

    /**
     * Is option present
     *
     * @param array $conditions
     * @return boolean
     */
    private function isOptionPresent($conditions)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from($this->getTable('catalog_product_bundle_option_value'));
        foreach ($conditions as $condition => $conditionValue) {
            $select->where($condition, $conditionValue);
        }
        $select->limit(1);

        $rowSelect = $connection->fetchRow($select);

        return (is_array($rowSelect) && !empty($rowSelect));
    }
}
