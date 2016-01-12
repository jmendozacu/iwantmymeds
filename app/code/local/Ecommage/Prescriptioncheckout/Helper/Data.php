<?php
class Ecommage_Prescriptioncheckout_Helper_Data extends Mage_Core_Helper_Data
{
    public function isActionAllowed($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('prescription/manage/' . $action);
    }

    public function getNewsItemInstance()
    {
        if (!$this->_newsItemInstance) {
            $this->_newsItemInstance = Mage::registry('prescription_item');

            if (!$this->_newsItemInstance) {
                Mage::throwException($this->__('prescription item instance does not exist in Registry'));
            }
        }

        return $this->_newsItemInstance;
    }


    public function checkPrescriptionInCart(){
        $cart = Mage::getModel('checkout/cart')->getQuote();
        $check = 0;
        foreach ($cart->getAllItems() as $item) {
            $productId = $item->getProduct()->getId();
            $products = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('is_prescription_product')
                ->addFieldToFilter('is_prescription_product', array("eq"=>1))
                ->addFieldToFilter('entity_id', array("eq"=>$productId));
            $productData = $products->getData();
            if($productData){
                $check++;
            }
        }
        return $check;
    }
}