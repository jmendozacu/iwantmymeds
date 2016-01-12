<?php

class Ecommage_Prescriptioncheckout_Block_Adminhtml_Prescription_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId   = 'id';
        $this->_blockGroup = 'prescriptioncheckout';
        $this->_controller = 'adminhtml_prescription';

        parent::__construct();


        $this->_updateButton('save', 'label', Mage::helper('prescriptioncheckout')->__('Save Item'));


        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
        ), -90);

        $this->_updateButton('delete', 'label', Mage::helper('prescriptioncheckout')->__('Delete Item'));


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
        $model = Mage::helper('prescriptioncheckout')->getNewsItemInstance();

        if ($model != null) {
            if ($model->getId()) {
                return Mage::helper('prescriptioncheckout')->__("Edit Prescription Attribute '%s'",
                    $this->escapeHtml($model->getTitle()));
            } else {
                return Mage::helper('prescriptioncheckout')->__('New Prescription Attribute');
            }
        }
    }





}