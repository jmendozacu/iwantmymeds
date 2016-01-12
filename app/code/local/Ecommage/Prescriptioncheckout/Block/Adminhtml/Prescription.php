<?php

class Ecommage_Prescriptioncheckout_Block_Adminhtml_Prescription extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'prescriptioncheckout';
        $this->_controller = 'adminhtml_prescription';
        $this->_headerText = Mage::helper('prescriptioncheckout')->__('Management Prescription');

        parent::__construct();
    }
}