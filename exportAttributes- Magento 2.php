<?php
/*
 * Lior G., Yotpo Support, March 4st 2018.
 * Yotpo Product Catalog Export Tool for Magento 2 - BETA v1.
 */

use Magento\Framework\App\Bootstrap;
include('app/bootstrap.php');

class Export{

    public static function exportFunc(){

		$bootstrap = Bootstrap::create(BP, $_SERVER);

    	$objectManager = $bootstrap->getObjectManager();

		$iteration = 0;
		$saveData = array();
		ob_start();
		header('Content-type: application/utf-8');
		header('Content-disposition: attachment; filename="Yotpo Catalog Export.csv"');
		$fp = fopen('php://output', 'w'); 

		$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface'); //Get store manager

		$productCollection = $objectManager->create('Magento\Catalog\Model\Product'); //Create product model from Object Manager

		$appState = $objectManager->get('Magento\Framework\App\State'); //Set area code 'global'
		$appState->setAreaCode('global');

		$products = $productCollection->getCollection(); //Get product collection 

		foreach ($products as $product) { //Go over products array, which is the product collection
			$_product = $objectManager->get('Magento\Catalog\Model\Product')->load($product->getId());
			$saveData['Product ID'] = $_product->getId();
			$saveData['Product Name'] = $_product->getName();
			$saveData['Product Description'] = $_product->getDescription();
			$saveData['Product URL'] = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB).$_product->getUrlKey().'.html';

			if ($_product->getImage() != 'no_selection'){ //Pull Image URL only in case there's a pic associated with the product
				$saveData['Product Image URL'] = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'catalog/product/'.$_product->getImage();
			}
			else { //If there's no image associated with the product, keep the column empty
				$saveData['Product Image URL'] = '';
			}

			$saveData['Product Price'] = $_product->getPrice();
			$saveData['Currency'] = 'USD'; //Statically set as USD as Yotpo currently supports USD only
			$saveData['Spec UPC'] = '';
			$saveData['Spec SKU'] = $_product->getSku();
			$saveData['Spec Brand'] = '';
			$saveData['Spec MPN'] = '';
			$saveData['Spec ISBN'] = '';
			$saveData['Blacklisted'] = 'false'; //Yotpo related
			$saveData['Product Group'] = ''; //Yotpo related
			if($iteration==0) fputcsv($fp, array_keys($saveData));
			fputcsv($fp, $saveData);
			$iteration++;
		}
	}
}

$var = Export::exportFunc();

?>