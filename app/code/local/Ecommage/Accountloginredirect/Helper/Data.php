<?php
class Ecommage_Accountloginredirect_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * Function get string url before customer login
     *
     * set string patch url to session
    */
    public function getUrlRedirectBackToLogin(){
        if (!Mage::getSingleton('customer/session')->isLoggedIn()){
            $base_url = Mage::getBaseUrl();
            $currentUrl = Mage::helper('core/url')->getCurrentUrl();
            if(str_replace($base_url,"",$currentUrl) != 'customer/account/login/' && str_replace($base_url,"",$currentUrl) != 'customer/account/logout/'){
                $url  = str_replace($base_url,"",$currentUrl);
                Mage::getSingleton('core/session')->setBackBeforeLoginUrl($url);
            }
        }
    }
}