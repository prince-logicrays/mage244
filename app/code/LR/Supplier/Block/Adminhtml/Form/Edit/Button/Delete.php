<?php

namespace LR\Supplier\Block\Adminhtml\Form\Edit\Button;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Delete extends Generic implements ButtonProviderInterface
{
    /**
     * @var Context
     */
    protected $context;
    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }

    /**
     * Get button data
     *
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        $entityId = $this->context->getRequest()->getParam('entity_id');
        if ($entityId) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getDeleteUrl()
    {
        $entityId = $this->context->getRequest()->getParam('entity_id');
        return $this->getUrl('*/*/delete', ['entity_id' => $entityId]);
    }
}
