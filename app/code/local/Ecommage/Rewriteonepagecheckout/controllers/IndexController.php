<?php
require_once(Mage::getModuleDir('controllers','IWD_Opc').DS.'IndexController.php');
class Ecommage_Rewriteonepagecheckout_IndexController extends IWD_Opc_IndexController{
    public function indexAction(){
        $quote = $this->getOnepage()->getQuote();
        $model_product = Mage::getModel('catalog/product');
        $check = 0;
        foreach ($quote->getAllItems() as $item){
            $product = $model_product->load($item->getProductId())->getData();
            if (isset($product['is_prescription_product'])){
                if ($product['is_prescription_product'] == 1){
                    $check++;
                }
            }
        }

        if($check == 0 || Mage::getSingleton('customer/session')->isLoggedIn()){
            if (!Mage::helper('checkout')->canOnepageCheckout()) {
                Mage::getSingleton('checkout/session')->addError($this->__('The onepage checkout is disabled.'));
                $this->_redirect('checkout/cart');
                return;
            }

            if (!$quote->hasItems() || $quote->getHasError()) {
                $this->_redirect('checkout/cart');
                return;
            }

            // init default address
            $this->initDefaultAddress();

            Mage::app()->getCacheInstance()->cleanType('layout');

            $this->updateDefaultPayment();

            if (!$quote->validateMinimumAmount()) {
                $error = Mage::getStoreConfig('sales/minimum_order/error_message') ?
                    Mage::getStoreConfig('sales/minimum_order/error_message') :
                    Mage::helper('checkout')->__('Subtotal must exceed minimum order amount');

                Mage::getSingleton('checkout/session')->addError($error);
                $this->_redirect('checkout/cart');
                return;
            }
            Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
            Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_secure' => true)));
            $this->getOnepage()->initCheckout();
            $this->loadLayout();
            $this->_initLayoutMessages('customer/session');
            $this->getLayout()->getBlock('head')->setTitle($this->__(Mage::getStoreConfig(self::XML_PATH_TITLE)));
            $this->renderLayout();
        }else{

            Mage::getSingleton('core/session')->addWarning('Please login first..');
            $this->_redirect('customer/account/login/');
        }
    }
}