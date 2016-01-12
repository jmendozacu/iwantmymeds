<?php
class Ecommage_Addmultiitemtocart_IndexController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        $productIds = $this->getRequest()->getParam('product-cart');
        try{
            $cart = Mage::helper('checkout/cart')->getCart();
            $msg = '';
            foreach($productIds as $productId) {
                $qty = 1;
                $product = Mage::getModel('catalog/product')->load($productId);
                $cart->addProduct($product, $qty);
            }
            $cart->save();
            $this->_redirect('checkout/cart');

        }
        catch(Exception $e) {
            Mage::getSingleton('core/session')->addError(Mage::helper('checkout')->__($e->getMessage()));
            $this->_redirect('checkout/cart');
        }
    }
}