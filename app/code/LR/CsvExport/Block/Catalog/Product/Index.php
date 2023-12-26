<?php

namespace LR\CsvExport\Block\Catalog\Product;

use Magento\Backend\Block\Template\Context;

class Index extends \Magento\Framework\View\Element\Template
{
    /**
     * Undocumented function
     *
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Get form action URL for POST booking request
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('inquiry/inquiry/form', ['_secure' => true]);
    }
}
