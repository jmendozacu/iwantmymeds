<?php
    require_once 'Mage/Customer/controllers/AccountController.php';
    class Ecommage_Accountloginredirect_AccountController extends Mage_Customer_AccountController{

        /**
         * Rewrite again target URL and redirect customer after logging in
         *
         *  redirect to current url before login
         *  Unset session url BackBeforeLoginUrl
         */


        protected function _loginPostRedirect()
        {
            $session = $this->_getSession();
            $urlRedirect = Mage::getSingleton('core/session')->getBackBeforeLoginUrl();
            if ($session->isLoggedIn()) {
                Mage::getSingleton('core/session')->unsBackBeforeLoginUrl();
//                use a switch statement and default redirects to account page.
                switch(strtoupper($urlRedirect)){
                    case "CHECKOUT/CART/":
                        $checkoutURL = Mage::helper('checkout/url')->getCheckoutUrl();
                        $session->setBeforeAuthUrl($checkoutURL);
                        break;
                    default:
                        $session->setBeforeAuthUrl($this->_getHelper('customer')->getAccountUrl());
                        break;
                }
            } else {
//                login failed, redirect back to the login page used.
                $session->setBeforeAuthUrl( $this->_getHelper('customer')->getLoginUrl());
            }
            $this->_redirectUrl($session->getBeforeAuthUrl(true));
        }

        public function logoutAction()
        {
            $session = Mage::getSingleton("core/session");
            $session->unsPerscriptionCheck();
            $session->unsCartAction();
            return parent::logoutAction();
        }
    }
