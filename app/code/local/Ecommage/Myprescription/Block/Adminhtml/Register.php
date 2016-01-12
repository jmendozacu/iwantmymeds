<?php
class Ecommage_Myprescription_Block_Adminhtml_Register extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        print('block');
        $this->_controller = 'adminhtml_register';
        $this->_blockGroup = 'myprescription';
        $this->_headerText = Mage::helper('myprescription')->__('Management Register');
        parent::__construct();
        $this->_removeButton('add');
    }
}