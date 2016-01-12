<?php
class Ecommage_Prescriptioncheckout_Model_Mysql4_Prescription extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('prescriptioncheckout/prescription', 'prescription_id');
    }
}