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

namespace Logicrays\Base\Block\Adminhtml;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\View\Helper\Js;
use Magento\Framework\View\LayoutFactory;
use Magento\Config\Block\System\Config\Form\Field;
use Logicrays\Base\Helper\Data;
use Logicrays\Base\Helper\Module;
use Logicrays\Base\Block\Adminhtml\Form\Element\Columns;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Config\Block\System\Config\Form\Fieldset;

class Modules extends Fieldset
{
    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var Data
     */
    private $logicraysHelper;

    /**
     * @var Module
     */
    private $moduleHelper;

    /**
     * @var Field
     */
    private $fieldRenderer;

    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Modules constructor.
     * @param Context $context
     * @param Session $authSession
     * @param Js $jsHelper
     * @param LayoutFactory $layoutFactory
     * @param Data $logicraysHelper
     * @param Module $moduleHelper
     * @param Curl $curl
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $authSession,
        Js $jsHelper,
        LayoutFactory $layoutFactory,
        Data $logicraysHelper,
        Module $moduleHelper,
        Curl $curl,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $authSession, $jsHelper, $data);
        $this->layoutFactory = $layoutFactory;
        $this->logicraysHelper = $logicraysHelper;
        $this->moduleHelper = $moduleHelper;
        $this->curl = $curl;
        $this->storeManager = $storeManager;
    }

    /**
     * Render function
     *
     * @param AbstractElement $element
     * @return array
     */
    public function render(AbstractElement $element)
    {
        $html = $this->_getHeaderHtml($element);
        $html .= $this->getTitleHtml($element);
        $localModules = $this->moduleHelper->getLogicraysModules();
        foreach ($localModules as $moduleName) {
            $html .= $this->getFieldHtml($element, $moduleName);
        }
        $html .= $this->_getFooterHtml($element);
        return $html;
    }

    /**
     * Get renderer object
     *
     * @return BlockInterface
     */
    protected function getFieldRenderer()
    {
        if (empty($this->fieldRenderer)) {
            $layout = $this->layoutFactory->create();

            $this->fieldRenderer = $layout->createBlock(
                Field::class
            );
        }
        return $this->fieldRenderer;
    }

    /**
     * Get html of title block.
     *
     * @param AbstractElement $fieldset
     * @return mixed
     */
    protected function getTitleHtml($fieldset)
    {
        $field = $fieldset->addField(
            'module_name',
            Columns::class,
            [
                'extension_name' =>$this->escapeHtml(__('Extension')),
                'current_ver'    =>$this->escapeHtml(__('Current Version')),
                'latest_ver'     =>$this->escapeHtml(__('Latest Version')),
                'change_log'     =>$this->escapeHtml(__('Change Log')),
                'user_guide'     =>$this->escapeHtml(__('User Guide'))
            ]
        )->setRenderer($this->getFieldRenderer());
        
        return $field->toHtml();
    }

    /**
     * Get the Html for the element.
     *
     * @param AbstractElement $fieldset
     * @param string $moduleCode
     * @return string
     */
    protected function getFieldHtml($fieldset, $moduleCode)
    {
        $localModule = $this->moduleHelper->getLocalModuleInfo($moduleCode);
        foreach ($localModule['autoload']['psr-4'] as $key => $value) {
            $moduleName = substr($key, 10);
            $moduleName = substr($moduleName, 0, -1);
        }
        if (empty($localModule)) {
            return '';
        }
        $suite = null;
        if (isset($localModule['extra']['suite'])) {
            $suite = $localModule['extra']['suite'];
        }
        if ($this->logicraysHelper->isModuleEnable('Logicrays_Breadcrumbs') && $suite == 'seo-suite') {
            return '';
        }
        $currentVersion = isset($localModule['extra']['suite-version']) ? $localModule['extra']['suite-version'] :
        $localModule['version'];

        $products = $this->getJsonObject();
        if (!$products) {
            return '';
        }
        foreach ($products as $productKey => $product) {
            
            if ($productKey == $moduleName) {
                $moduleName = ($product->product_name) ?
                        $product->product_name : "not define";
                $latestVersion = (version_compare($currentVersion, $product->version) < 0) ?
                    $product->version : "Up To date";
                $changeLogUrl = ($product->change_log_url) ?
                        $product->change_log_url : "not define";
                $userGuideUrl = ($product->user_guide_url) ?
                        $product->user_guide_url : "not define";
                $extensionUrl = ($product->product_url) ?
                        $product->product_url : "not define";
            }
        }
        $extensionName = "<a href='$extensionUrl' target ='_blank'>$moduleName</a>";
        $changeLog = "<a href='$changeLogUrl' target ='_blank'>Change Log</a>";
        $userGuide = "<a href='$userGuideUrl' target ='_blank'>User Guide</a>";
        
        $field = $fieldset->addField(
            $moduleCode,
            Columns::class,
            [
                'extension_name'    => $extensionName,
                'current_ver'       => $currentVersion,
                'latest_ver'        => $latestVersion,
                'change_log'        => $changeLog,
                'user_guide'        => $userGuide
                
            ]
        )->setRenderer($this->getFieldRenderer());
        return $field->toHtml();
    }

    /**
     * Return header html for fieldset
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getHeaderHtml($element)
    {
        if ($element->getIsNested()) {
            $html = '<tr class="nested"><td colspan="4"><div class="' . $this->_getFrontendClass($element) . '">';
        } else {
            $html = '<div class="' . $this->_getFrontendClass($element) . '">';
        }

        $html .= '<div class="entry-edit-head admin__collapsible-block">' .
            '<span id="' .
            $element->getHtmlId() .
            '-link" class="entry-edit-head-link"></span>';

        $html .= $this->_getHeaderTitleHtml($element);

        $html .= '</div>';
        $html .= '<input id="' .
            $element->getHtmlId() .
            '-state" name="config_state[' .
            $element->getId() .
            ']" type="hidden" value="' .
            (int)$this->_isCollapseState(
                $element
            ) . '" />';
        $html .= '<fieldset class="' . $this->_getFieldsetCss() . '" id="' . $element->getHtmlId() . '">';
        $html .= '<legend>' . $element->getLegend() . '</legend>';

        $html .= $this->_getHeaderCommentHtml($element);

        $html .= '<table cellspacing="0" class="form-list"><colgroup class="lr-label"/><colgroup class="lr-value"/>';
        if ($this->getRequest()->getParam('website') || $this->getRequest()->getParam('store')) {
            $html .= '<colgroup class="use-default" />';
        }
        $html .= '<colgroup class="scope-label" /><colgroup class="" /><tbody>';

        return $html;
    }

    /**
     * For getting information of release magento extension.
     *
     * @return void
     */
    public function getJsonObject()
    {
        $url = $this->storeManager->getStore()->getBaseUrl() . 'media/moduleVersionInformation.json';
        $this->curl->post($url, []);
        $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
        $result = $this->curl->getBody();
        if ($result) {
            $obj = json_decode($result);
            if ($obj) {
                return $obj;
            }
        }
        return null;
    }
}
