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
 * @package     Logicrays_Base
 * @copyright   Copyright (c) Logicrays (https://www.logicrays.com/)
 */
declare(strict_types=1);

namespace Logicrays\Base\Block\Adminhtml\Form\Element;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Columns extends AbstractElement
{
    /**
     * Init type
     */
    protected function _construct()
    {
        $this->setType('columns');
        parent::_construct();
    }

    /**
     * Get the html.
     *
     * @return mixed
     */
    public function getHtml()
    {
        if ($this->getRequired()) {
            $this->addClass('required-entry _required');
        }

        return $this->getDefaultHtml();
    }

    /**
     * Get the default html.
     *
     * @return mixed
     */
    public function getDefaultHtml()
    {
        $html = $this->getData('default_html');
        if ($html === null) {
            $html .= '<tr>';
            $html .= $this->getElementHtml();
            $html .= '</tr>';
        }
        return $html;
    }

    /**
     * Get the Html for the element.
     *
     * @return string
     */
    public function getElementHtml()
    {
        $columns = $this->getVersionHtml($this->getData('extension_name'));
        $columns .= $this->getVersionHtml($this->getData('current_ver'));
        $columns .= $this->getVersionHtml($this->getData('latest_ver'));
        $columns .= $this->getVersionHtml($this->getData('change_log'));
        $columns .= $this->getVersionHtml($this->getData('user_guide'));
        
        $html = $this->getBeforeElementHtml() . $columns . $this->getAfterElementHtml();
        return $html;
    }

    /**
     * Get the html for module versions.
     *
     * @param string $value
     * @return string
     */
    protected function getVersionHtml($value)
    {
        $width = '20%';
        $html = '<td class="lr-value ' . '" style=" width: ' . $width . '; padding: 2.2rem 1.5rem 0 0;">';
        $html .= "<div class='lr-version' style='text-align: center; " . "'>$value</div>";
        $html .= '</td>';
        return $html;
    }
}
