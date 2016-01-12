<?php
class Ecommage_Myprescription_Helper_Data extends Mage_Core_Helper_Data{
    private $collection;
    // Get all product
    public function getCollectionCatalog(){
        $collection = Mage::getModel('catalog/product')
            ->getCollection();
        $collection
            ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
            ->addAttributeToSelect('*');
        return $collection;
    }
    // search with attribute Myprescription product

    public function getCollectionPrescription($param){
        switch ($param) {
            case '1':
                $lable = 'Electronic';
                break;
            case '2':
                $lable = 'NHS';
                break;
            case '3':
                $lable = 'Private';
                break;
            case '4':
                $lable = 'Repeat';
                break;
            case '5':
                $lable = 'Vet';
                break;
            default:
                return false;
                break;
        }
        // get value attribute
        $attributeInfo = Mage::getResourceModel('eav/entity_attribute_collection')->setCodeFilter('prescription_type')->getFirstItem();
        $attributeId = $attributeInfo->getAttributeId();
        $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
        $attributeOptions = $attribute ->getSource()->getAllOptions(false);

        foreach($attributeOptions as $attributeOption){
            if($attributeOption['label'] == $lable){
                $finset = $attributeOption['value'];
            }
        }
        // get collection
        $collection = $this->getCollectionCatalog();
        $data_product = $collection
            ->addAttributeToFilter('prescription_type', array('finset' => array($finset)));
        return $data_product;
    }


    // result with param search
    public function getResult($a){
        return $this->getCollectionPrescription($a);
    }

    public function getNewsItemInstance()
    {
        if (!$this->_newsItemInstance) {
            $this->_newsItemInstance = Mage::registry('register_item');

            if (!$this->_newsItemInstance) {
                Mage::throwException($this->__('Register item instance does not exist in Registry'));
            }
        }

        return $this->_newsItemInstance;
    }

}
