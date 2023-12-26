<?php
use Magento\Framework\App\Bootstrap;
require __DIR__ . '/../app/bootstrap.php';
ini_set('display_errors', 1);
ini_set('max_execution_time', "-1");
ini_set('memory_limit', "-1");

// Set up bootstrap environment
$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();

$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('adminhtml');
// Set up file path and name
$filePath = $objectManager->get('Magento\Framework\Filesystem');

$path =  $filePath->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath();
$name = 'productexport.csv';
$file = $path . '/' . $name;
$fp = fopen($file, 'w');

$header = ['Handle','Title','Body (HTML)','Vendor','Product Category','Type','Tags','Published','Option Name','Option Value','Variant Sku', 'Variant Grams','Variant Inventory Qty','Variant Price','Variant Compare At Price','Image Src','Image Position','Image Alt Text','SEO Title','SEO Description','Variant Weight Unit','Cost per item','Price / International','Compare At Price / International','Status'];
fputcsv($fp, $header);

// Set up page size and current page variables
// $currentpage = 1;
// $pageSize = 10;

// $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
// $totalProducts = $productCollection->getSize();

// for ($currentPage = 1; $currentPage <= ceil($totalProducts / $pageSize); $currentPage++) {
//     $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
//     $productCollection->setPageSize($pageSize)->setCurPage($currentPage);

$batchSize = 1000;
$currentPage = 1;
$path = 'tax/classes/default_customer_tax_class';
$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
$scopeInterface = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
$taxRateId = $scopeInterface->getValue($path, $storeScope);

$taxCalculation = $objectManager->create('Magento\Tax\Model\Calculation\Rate')->load($taxRateId,'tax_calculation_rate_id' );
$rate = $taxCalculation->getRate();
$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
if (str_contains($useragent, 'Linux')) {
    echo "call";
} else {
    echo "not call";
}
echo "<b>Your User Agent is</b>: " . $useragent;exit;

$productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
$productCollection->setPageSize($batchSize);

do {
    $productCollection->setCurPage($currentPage);
    $productCollection->load();

        $productData = [];
        foreach ($productCollection as $productCollectionData) {
            if (!empty($productCollectionData->getId())) {
                $productFactory = $objectManager->create('Magento\Catalog\Model\ProductFactory')->create();
                $repository = $objectManager->create('Magento\Catalog\Model\ProductRepository');
                $product = $productFactory->load($productCollectionData->getId());
                $productData[] = $product->getDeposit();
            }
        }
        echo "<pre>";
        print_r($productData);exit;

        // Clear the product collection
        $productCollection->clear();

    // Increment current page
    $currentPage++;
} while ($productCollection->getSize() > 0);
// }

fclose($fp);
echo 'Product data exported successfully!';
