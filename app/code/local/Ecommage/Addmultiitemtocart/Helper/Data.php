<?php
class Ecommage_Addmultiitemtocart_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * @return array
     * get all item id product
     */
    public function getIdProductInCurrentCart(){
        $productIds = array();
        $cart = Mage::getModel('checkout/cart')->getQuote();
        foreach ($cart->getAllItems() as $item) {
            $productIds[] = $item->getProduct()->getId();
        }
        return $productIds;
    }
}