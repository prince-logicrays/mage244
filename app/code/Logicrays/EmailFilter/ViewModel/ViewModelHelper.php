<?php
/**
 * Logicrays
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Logicrays
 * @package     Logicrays_EmailFilter
 * @copyright   Copyright (c) Logicrays (https://www.logicrays.com/)
 */
namespace Logicrays\EmailFilter\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Logicrays\EmailFilter\Helper\Data;
use Magento\Contact\Helper\Data as MagentoHelperContactData;

/**
 * allow passing data and additional functionality to the template file
 */
class ViewModelHelper implements ArgumentInterface
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var MagentoHelperContactData
     */
    protected $contactData;

    /**
     * @param Data $helperData
     * @param MagentoHelperContactData $contactData
     */
    public function __construct(
        Data $helperData,
        MagentoHelperContactData $contactData,
    ) {
        $this->helperData = $helperData;
        $this->contactData = $contactData;
    }

    /**
     * Return Object of Helper Data Class.
     *
     * @return object
     */
    public function getHelperData()
    {
        return $this->helperData;
    }

    /**
     * Return Object of Data Class.
     *
     * @return object
     */
    public function getContactHelperDataObject()
    {
        return $this->contactData;
    }
}
