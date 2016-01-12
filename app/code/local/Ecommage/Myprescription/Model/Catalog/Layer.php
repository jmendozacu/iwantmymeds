<?php
class Ecommage_Myprescription_Model_Catalog_Layer  extends Mage_Catalog_Model_Layer{

      public function getProductCollection(){
        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
        } else {
            $_post = Mage::app()->getRequest()->getParams();
            if(isset($_post['param'])){
                $helper = Mage::helper("myprescription");
                $param = $_post['param'];
                $collection = $helper->getResult($param);
                if(isset($_post['search_name'])){
                    $search_name = $_post['search_name'];
                    $collection->addAttributeToFilter('is_prescription_product', array('eq' => '1'));
                    $collection->addFieldToFilter('name',array("like"=>'%'.$search_name.'%'));
                }
                $collection
                    ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                    ->addMinimalPrice()
                    ->addFinalPrice()
                    ->addTaxPercents();
                // get to finter with collection
                Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
                Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
                $this->prepareProductCollection($collection);
                $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
            }else{
                $collection = $this->getCurrentCategory()->getProductCollection();
                $this->prepareProductCollection($collection);
                $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
            }

        }
        return $collection;
    }

}