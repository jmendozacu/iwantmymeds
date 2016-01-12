<?php
class Ecommage_Accountloginredirect_Block_Redirect extends Mage_Core_Block_Template
{
    /**
     * @valiable call to opject Accountloginredirect/Helper/Data.php
    */
    private $_accountloginredirectHelper;

    public function __construct() {
        $this->_accountloginredirectHelper = Mage::helper('accountloginredirect');
        parent::__construct();
    }
    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function getUrlRedirectBackToLogin(){
        $this->_accountloginredirectHelper->getUrlRedirectBackToLogin();
    }
}