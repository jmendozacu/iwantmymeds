<?php
echo "string";
require 'app/Mage.php';
umask(0);

Mage::app('admin');
Mage::setIsDeveloperMode(true);

$productCollection = Mage::getResourceModel('catalog/product_collection');
// var_dump($productCollection->getData());
foreach($productCollection as $product){
	try {
		// var_dump(Mage::getModel('catalog/product')->load($product->getId())->getData());
		$product->delete();
		echo "deleted";
	} catch (Exception $e) {
		var_dump($e->getMessage());
	}
	
// echo $product->getId();
// echo "<br/>";
//          $MediaDir=Mage::getConfig()->getOptions()->getMediaDir();
//         echo $MediaCatalogDir=$MediaDir .DS . 'catalog' . DS . 'product';
// echo "<br/>";

// $MediaGallery=Mage::getModel('catalog/product_attribute_media_api')->items($product->getId());
// echo "<pre>";
// print_r($MediaGallery);
// echo "</pre>";

//     foreach($MediaGallery as $eachImge){
//         $MediaDir=Mage::getConfig()->getOptions()->getMediaDir();
//         $MediaCatalogDir=$MediaDir .DS . 'catalog' . DS . 'product';
//         $DirImagePath=str_replace("/",DS,$eachImge['file']);
//         $DirImagePath=$DirImagePath;

//         $remove=Mage::getModel('catalog/product_attribute_media_api')->remove($product->getId(),$eachImge['file']);
//     }


}