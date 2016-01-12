<?php
require_once 'Mage/Checkout/controllers/CartController.php';

class Ecommage_Addmultiitemtocart_Checkout_CartController extends
    Mage_Checkout_CartController
{

    public function addmultipleAction()
    {
        $productIds = $this->getRequest()->getParams();
        if (!is_array($productIds)) {
            $this->_goBack();
            return;
        }

        foreach ($productIds as $productId) {
            try {
                $qty = 1;


                $cart = $this->_getCart();
                $product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($productId);
                $eventArgs = array(
                    'product' => $product,
                    'qty' => $qty,
                    'additional_ids' => array(),
                    'request' => $this->getRequest(),
                    'response' => $this->getResponse(),
                );

                Mage::dispatchEvent('checkout_cart_before_add', $eventArgs);

                $cart->addProduct($product, $qty);

                Mage::dispatchEvent('checkout_cart_after_add', $eventArgs);

                $cart->save();

                Mage::dispatchEvent('checkout_cart_add_product', array('product' => $product));

                $message = $this->__('%s was successfully added to your shopping cart.', $product->getName());
                Mage::getSingleton('checkout/session')->addSuccess($message);
            } catch (Mage_Core_Exception $e) {
                if (Mage::getSingleton('checkout/session')->getUseNotice(true)) {
                    Mage::getSingleton('checkout/session')->addNotice($product->getName(). ': ' . $e->getMessage());
                } else {
                    Mage::getSingleton('checkout/session')->addError($product->getName() .': ' . $e->getMessage());
                }
            } catch (Exception $e) {
                Mage::getSingleton('checkout/session')->addException($e,
                    $this->__('Can not add item to shopping cart'));
            }
        }
        $this->_goBack();
    }
}