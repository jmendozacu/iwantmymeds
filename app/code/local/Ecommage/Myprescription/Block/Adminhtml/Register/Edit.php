<?php
class Ecommage_Myprescription_Block_Adminhtml_Register_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId   = 'id';
        $this->_blockGroup = 'myprescription';
        $this->_controller = 'adminhtml_register';

        parent::__construct();


        $this->_updateButton('save', 'label', Mage::helper('myprescription')->__('Save Register'));


        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
        ), -90);

//        $this->_updateButton('delete', 'label', Mage::helper('myprescription')->__('Delete Register'));


        $this->_formScripts[] = "
                function toggleEditor() {
                    if (tinyMCE.getInstanceById('page_content') == null) {
                        tinyMCE.execCommand('mceAddControl', false, 'page_content');
                    } else {
                        tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                    }
                }

                function saveAndContinueEdit(){
                    editForm.submit($('edit_form').action+'back/edit/');
                }
            ";
    }

    public function getHeaderText()
    {

        $model = Mage::helper('myprescription')->getNewsItemInstance();

        if ($model != null) {
            if ($model->getId()) {
                return Mage::helper('myprescription')->__("Edit Register Attribute '%s'",
                    $this->escapeHtml($model->getTitle()));
            } else {
                return Mage::helper('myprescription')->__('New Register Attribute');
            }
        }
    }


}