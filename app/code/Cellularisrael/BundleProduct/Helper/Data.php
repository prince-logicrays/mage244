<?php
namespace Cellularisrael\BundleProduct\Helper;
use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    protected $_storeManager;
    protected $_currencyFactory;
    protected $_productCollectionFactory;
    protected $_productFactory;
    protected $timezone;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_currencyFactory = $currencyFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_productFactory = $productFactory;
        $this->timezone = $timezone;
    }

    public function getCurrencySymbol($currencyCode = NULL)
    {
        if($currencyCode == NULL){
            $currencyCode = $this->_storeManager->getStore()->getCurrentCurrencyCode();
        }
        $currency = $this->_currencyFactory->create()->load($currencyCode);
        return $currency->getCurrencySymbol();
    }

    public function getPlanProductPrice($product, $itemid, $displayCartPricesInclTax)
    {
      // set default because number_format() is called on this return value
        $planitemtotal = 0;
        if ($product->getTypeId() == 'bundle') {

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $cart = $objectManager->get('\Magento\Checkout\Model\Cart');

            $items = $cart->getItems();

            foreach ($items as $item) {
                if ($item->getParentItemId() == $itemid) {
                    if ($item->getIsPlan()) {
                        if ($displayCartPricesInclTax== 1) {
                            $planitemtotal = $item->getBaseRowTotalInclTax();
                        } else {
                            $planitemtotal = $item->getBaseRowTotal();
                        }
                    }
                }
            }
        }
        return $planitemtotal;
    }

    public function getPlanProduct($product , $itemid)
    {
        if($product->getTypeId() == 'bundle'){

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $cart = $objectManager->get('\Magento\Checkout\Model\Cart');

            $items = $cart->getItems();

            foreach ($items as $item) {
                if($item->getParentItemId() == $itemid){
                    $itemIds[] = $item->getProductId();
                }
            }

            //changes gor plan product in cart item

            /*
            $selectionCollection = $product->getTypeInstance(true)
                ->getSelectionsCollection($product->getTypeInstance(true)->getOptionsIds($product), $product);

            $itemIds = array();

            foreach ($selectionCollection as $item) {
                $itemIds[] = $item->getId();
            } */

            $collection = $this->_productCollectionFactory->create()
                        ->addAttributeToSelect('*')
                        ->addAttributeToFilter('entity_id', ['in' => $itemIds])
                        ->addAttributeToFilter('is_plan', 1);

            if($collection->getSize()){
                $productId = $collection->getFirstItem()->getId();
                return $this->_productFactory->create()->load($productId);
            }
        }

        return $product;
    }

    public function getFormatedDate($date)
    {
        if (empty($date) || $date == '0000-00-00') {
            return '-';
        }
        return $this->timezone->date(new \DateTime($date))->format('m/d/Y');
    }

    public function getFormatedDateProvisionEmail($date)
    {
        if (empty($date) || $date === '-' || $date == '0000-00-00') {
            return '-';
        }
        return $this->timezone->date(new \DateTime($date))->format('m/d/Y');
    }

    public function checkdatavalidate($date){
        $arr=explode("-",$date); // breaking string to create an array
        //echo "<pre>";
        //print_r($arr);die;
        $yy=$arr[0]; // first element of the array is month
        $mm=$arr[1]; // second element is date
        $dd=$arr[2]; // third element is year
        If(!checkdate($mm,$dd,$yy)){
            return 0;
        }else {
            return 1;
        }
    }

    public function getFormatedDateTask($date)
    {
        if($date == '' || $date == '0000-00-00'){
            return '-';
        }
        return $this->timezone->date(new \DateTime($date))->format('M d,Y');
    }

    /**
     * Check image exist
     *
     * @param string $url
     * @return bool
     */
    public function remoteImageExists($url)
    {
        $headers = @get_headers($url);
        return stripos($headers[0], "200 OK") ? true : false;
    }

    public function getDeviceExpirationFormattedDate($date)
    {
        if (empty($date) || $date == '0000-00-00') {
            return '-';
        }
        return $this->timezone->date(new \DateTime($date))->format('m/d/Y');
    }

    /**
     * Get Popup Message for Long Term Plan Have No End Date function
     *
     * @return string|null
     */
    public function getPopupMessageForLTPlanNoEndDate()
    {
        $path = 'long_term_plan_no_end_date_message/general/popup_text';

        $config =  $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return isset($config) ? (string) $config : '';
    }
}
