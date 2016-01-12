<?php
class Ecommage_Myprescription_Model_Mysql4_Myprescription extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('ecommage_myprescription/myprescription', 'id');
    }
}