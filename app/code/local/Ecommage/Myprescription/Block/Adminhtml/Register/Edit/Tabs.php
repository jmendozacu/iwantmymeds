<?php
class Ecommage_Myprescription_Block_Adminhtml_Register_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('myprescription')->__('Register Attribute'));
    }
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label' => Mage::helper('myprescription')->__('Register Information'),
            'title' => Mage::helper('myprescription')->__('Register Information'),
            'content' => $this->getLayout()
                ->createBlock('myprescription/adminhtml_register_edit_tab_main')
                ->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}
