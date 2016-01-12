<?php
class Ecommage_Prescriptioncheckout_Model_Mysql4_Prescription_Collection extends Varien_Data_Collection_Db
{
    protected $_shipTable;

    public function __construct()
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('prescriptioncheckout_read'));
        $this->_shipTable = Mage::getSingleton('core/resource')->getTableName('ecommage_prescription');
        $this->_select->from(array("s" => $this->_shipTable))
            ->order(array("prescription_id"));
        $this->_setIdFieldName('prescription_id');
        return $this;
    }
}