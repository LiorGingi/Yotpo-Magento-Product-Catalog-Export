<?php
/*
 * Lior G., Yotpo Support, March 1st 2018.
 * Yotpo Product Catalog Export Tool for Magento 1 - BETA v1.
 */

require_once 'app/Mage.php';
Mage::app(Mage_Core_Model_App::ADMIN_STORE_ID);

$iteration = 0;
$saveData = array();
ob_start();
header('Content-type: application/utf-8');
header('Content-disposition: attachment; filename="Yotpo Catalog Export.csv"');
$fp = fopen('php://output', 'w'); 

$products = Mage::getModel('catalog/product')->getCollection();

foreach ($products as $product) {
	$_product = Mage::getModel('catalog/product')->load($product->getId());
	$saveData['Product ID'] = $_product->getId();
	$saveData['Product Name'] = $_product->getName();
	$saveData['Product Description'] = $_product->getDescription();
	$saveData['Product URL'] = str_replace('exportAttributes.php/','',Mage::getBaseUrl()).$_product->getUrlPath();

	if ($_product->getImage() != NULL){ //Pull Image URL only in case there's a pic associated with the product
		$saveData['Product Image URL'] = Mage::getModel('catalog/product_media_config')->getMediaUrl( $_product->getImage());
	}
	else { //If there's no image associated with the product, keep the column empty
		$saveData['Product Image URL'] = '';
	}

	$saveData['Product Price'] = $_product->getPrice();
	$saveData['Currency'] = 'USD';
	$saveData['Spec UPC'] = '';
	$saveData['Spec SKU'] = $_product->getSku();
	$saveData['Spec Brand'] = '';
	$saveData['Spec MPN'] = '';
	$saveData['Spec ISBN'] = '';
	$saveData['Blacklisted'] = 'false';
	$saveData['Product Group'] = '';
	if($iteration==0) fputcsv($fp, array_keys($saveData));
	fputcsv($fp, $saveData);
	$iteration++;
	}
?>
