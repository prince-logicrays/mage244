<?php

namespace TDD\TopMenuImage\Block\Html;

use Magento\Framework\Data\Tree\Node;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Data\TreeFactory;

class Topmenu extends \Magento\Theme\Block\Html\Topmenu
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var NodeFactory
     */
    protected $nodeFactory;

    /**
     * @var TreeFactory
     */
    protected $treeFactory;

    /**
     * @var CollectionFactory
     */
    protected $_categoryFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param Template\Context $context
     * @param NodeFactory $nodeFactory
     * @param TreeFactory $treeFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collecionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collecionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($context, $nodeFactory, $treeFactory, $data);
        $this->_categoryFactory = $collecionFactory;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * Get top menu html
     *
     * @param \Magento\Framework\Data\Tree\Node $menuTree
     * @param [type] $childrenWrapClass
     * @param [type] $limit
     * @param array $colBrakes
     * @return void
     */
    protected function _getHtml(
        \Magento\Framework\Data\Tree\Node $menuTree,
        $childrenWrapClass,
        $limit,
        array $colBrakes = []
    ) {
        $html = '';

        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = $parentLevel === null ? 0 : $parentLevel + 1;

        $counter = 1;
        $itemPosition = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

        /** @var \Magento\Framework\Data\Tree\Node $child */
        foreach ($children as $child) {
            if ($childLevel === 0 && $child->getData('is_parent_active') === false) {
                continue;
            }
            $child->setLevel($childLevel);
            $child->setIsFirst($counter == 1);
            $child->setIsLast($counter == $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);

            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel == 0 && $outermostClass) {
                $outermostClassCode = ' class="' . $outermostClass . '" ';
                $currentClass = $child->getClass();

                if (empty($currentClass)) {
                    $child->setClass($outermostClass);
                } else {
                    $child->setClass($currentClass . ' ' . $outermostClass);
                }
            }

            if (is_array($colBrakes) && count($colBrakes) && $colBrakes[$counter]['colbrake']) {
                $html .= '</ul></li><li class="column"><ul>';
            }

            $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
            $html .= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . '><span>' .
            $this->escapeHtml($child->getName()) . $this->getCustomThumbnail($child) . '</span></a>' .
            $this->_addSubMenu($child, $childLevel, $childrenWrapClass, $limit) . '</li>';
            $itemPosition++;
            $counter++;
        }

        if (is_array($colBrakes) && count($colBrakes) && $limit) {
            $html = '<li class="column"><ul>' . $html . '</ul></li>';
        }

        return $html;
    }

    /**
     * Get custom thumbnail
     *
     * @param array $childObj
     * @return string
     */
    public function getCustomThumbnail($childObj)
    {
        // if (!($childObj->getIsCategory() && $childObj->getLevel() == 1)) {
        //     return false;
        // }

        $store = $this->_storeManager->getStore();
        $baseUrl = $store->getBaseUrl();
        $mediaBaseUrl = $store->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        $catNodeArr = explode('-', $childObj->getId());
        $catId = end($catNodeArr);

        $collection = $this->_categoryFactory
                ->create()
                ->addAttributeToSelect('cat_thumbnail')
                ->addAttributeToFilter('entity_id', ['eq'=>$catId])
                ->setPageSize(1);

        if ($collection->getSize() && $collection->getFirstItem()->getCatThumbnail()) {
            $catThumbnailUrl = $baseUrl . $collection->getFirstItem()->getCatThumbnail();
        } else {
            $placeholderImage = $this->_scopeConfig->getValue('catalog/placeholder/thumbnail_placeholder');
            $catThumbnailUrl = $mediaBaseUrl . 'catalog/product/placeholder/' . $placeholderImage;
        }

        // if ($collection->getSize() && $collection->getFirstItem()->getThumbnail()) {
        //     $catThumbnailUrl = $mediaBaseUrl.'catalog/category/'.$collection->getFirstItem()->getThumbnail();
        // } else {
        //     $placeholderImage = $this->_scopeConfig->getValue('catalog/placeholder/thumbnail_placeholder');
            
        //     $catThumbnailUrl = $mediaBaseUrl . 'catalog/product/placeholder/' . $placeholderImage;
        // }
            return '<span class="cat-thumbnail"><img src="'.$catThumbnailUrl.'"></span>';
    }
}
