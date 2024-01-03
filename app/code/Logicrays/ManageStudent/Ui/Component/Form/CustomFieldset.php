<?php

namespace Logicrays\ManageStudent\Ui\Component\Form;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\ComponentVisibilityInterface;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Fieldset
 * @package Logicrays\ManageStudent\Ui\Component\Form
 */
class CustomFieldset extends Fieldset
{
    /**
     * @var \Logicrays\ManageStudent\Helper\Data
     */
    protected $helper;

    protected $storeManager;

    /**
     * RuleInformationFieldset constructor.
     * @param ContextInterface $context
     * @param array $components
     * @param array $data
     * @param \Logicrays\ManageStudent\Helper\Data $helper
     */
    public function __construct(
        \Logicrays\ManageStudent\Helper\Data $helper,
        StoreManagerInterface $storeManager,
        ContextInterface $context,
        array $components = [],
        array $data = []
    ) {
        $this->helper = $helper;
        $this->storeManager = $storeManager;
        parent::__construct($context, $components, $data);
    }

    /**
     * hide or show fieldset based on module enabled or disabled.
     */
    public function prepare()
    {
        $visiable = false;
        $config = $this->getData('config');
        $store_id = $this->getStoreId();
        if($this->helper->isEnabled($store_id) ) {
            $visiable = true;
        }

        $config['visible'] = $visiable;
        $this->setData('config', $config);
        parent::prepare();
    }

    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }
}