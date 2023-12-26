<?php
declare(strict_types=1);
/**
 * Logicrays
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Logicrays
 * @package     Logicrays_ManageCategoryImage
 * @copyright   Copyright (c) Logicrays (https://www.logicrays.com/)
 */

namespace Logicrays\ManageCategoryImage\Block\Adminhtml\Button;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Delete extends Generic implements ButtonProviderInterface
{
    /**
     * Context variable
     *
     * @var Context
     */
    protected $context;

    /**
     * __construct function
     *
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }

    /**
     * GetButtonData function
     *
     * @return string
     */
    public function getButtonData()
    {
        $data = [];
        $id = $this->context->getRequest()->getParam('id');

        if ($id) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to delete?'
                ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 15,
            ];
        }
        return $data;
    }

    /**
     * GetDeleteUrl function
     *
     * @return array
     */
    public function getDeleteUrl()
    {
        $id = $this->context->getRequest()->getParam('id');
        return $this->getUrl('*/*/delete', ['id' => $id]);
    }
}
