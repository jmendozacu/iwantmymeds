<?php
require_once "Mage/Checkout/controllers/OnepageController.php";  
class Ecommage_Prescriptioncheckout_Checkout_OnepageController extends Mage_Checkout_OnepageController{

    public function indexAction()
    {
        $customerData = Mage::getSingleton('customer/session')->getCustomer();
        $customer = Mage::getModel('customer/customer')->load($customerData->getId());
        $prescription = unserialize($customer->getPrescriptionAttribute());

        $checkPrescription = Mage::helper('prescriptioncheckout')->checkPrescriptionInCart();
        if($checkPrescription >0 && $prescription == null){
            Mage::getSingleton('checkout/session')->addError($this->__('You can select a prescription attribute'));
            $this->_redirect('checkout/cart');
        }



        if (!Mage::helper('checkout')->canOnepageCheckout()) {
            Mage::getSingleton('checkout/session')->addError($this->__('The onepage checkout is disabled.'));
            $this->_redirect('checkout/cart');
            return;
        }
        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->_redirect('checkout/cart');
            return;
        }
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
        $this->getLayout()->getBlock('head')->setTitle($this->__('Checkout'));
        $this->renderLayout();
    }
}
				