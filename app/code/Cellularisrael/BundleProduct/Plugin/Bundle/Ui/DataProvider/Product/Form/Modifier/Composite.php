<?php
namespace Cellularisrael\BundleProduct\Plugin\Bundle\Ui\DataProvider\Product\Form\Modifier;

use Magento\Bundle\Ui\DataProvider\Product\Form\Modifier\Composite as BundleComposite;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Bundle\Model\Product\Type;
use Magento\Bundle\Ui\DataProvider\Product\Form\Modifier\BundlePanel;
use Magento\Bundle\Model\Selection;

class Composite
{
    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var Selection
     */
    protected $selection;

    /**
     * @param LocatorInterface $locator
     * @param Selection $selection
     */
    public function __construct(
        LocatorInterface $locator,
        Selection $selection
    ) {
        $this->locator = $locator;
        $this->selection = $selection;
    }

    /**
     * After Modify Data function
     *
     * @param BundleComposite $subject
     * @param array $data
     * @return array
     */
    public function afterModifyData(BundleComposite $subject, array $data)
    {
        $product = $this->locator->getProduct();
        $modelId = $product->getId();
        $isBundleProduct = $product->getTypeId() === Type::TYPE_CODE;
        if ($isBundleProduct && $modelId) {
            foreach ($data[$modelId][BundlePanel::CODE_BUNDLE_OPTIONS][BundlePanel::CODE_BUNDLE_OPTIONS] as &$option) {
                foreach ($option['bundle_selections'] as &$selection) {
                    $this->selection->load($selection['selection_id']);
                    $selection['bundle_popup_message'] = $this->selection->getBundlePopupMessage();
                }
            }
        }

        return $data;
    }
}
