<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Block_Adminhtml_Ebay_Listing_AutoAction_Mode_Global
    extends Ess_M2ePro_Block_Adminhtml_Listing_AutoAction_Mode_Global
{
    // ####################################

    public function __construct()
    {
        parent::__construct();

        // Initialization block
        //------------------------------
        $this->setId('ebayListingAutoActionModeGlobal');
        //------------------------------

        $this->setTemplate('M2ePro/ebay/listing/auto_action/mode/global.phtml');
    }

    // ####################################

    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();

        //------------------------------
        $breadcrumb = $this->getLayout()->createBlock('M2ePro/adminhtml_ebay_listing_autoAction_mode_breadcrumb');
        $breadcrumb->setData('step', 1);
        $this->setChild('breadcrumb', $breadcrumb);
        //------------------------------
    }

    // ####################################
}
