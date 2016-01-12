<?php
/**
 * News List admin edit form main tab
 *
 * @author Magento
 */
class Ecommage_Myprescription_Block_Adminhtml_Register_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    protected function _prepareForm()
    {
        $model = Mage::helper('myprescription')->getNewsItemInstance();

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('news_main_');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('myprescription')->__('Register Item Info')
        ));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array(
                'name' => 'id',
            ));
        }
        $fieldset->addField('name_and_address', 'text', array(
            'label'        => Mage::helper('myprescription')->__('Name and address'),
            'class'        => 'required-entry',
            'required'    => true,
            'name'        => 'name_and_address',
        ));

        $fieldset->addField('telephone_number', 'text', array(
            'label'        => Mage::helper('myprescription')->__('Telephone Number'),
            'class'        => 'required-entry',
            'required'    => true,
            'name'        => 'telephone_number',
        ));
        $fieldset->addField('mobile', 'text', array(
            'label'        => Mage::helper('myprescription')->__('Mobile'),
            'class'        => 'required-entry',
            'required'    => true,
            'name'        => 'mobile',
        ));
        $fieldset->addField('email', 'text', array(
            'label'        => Mage::helper('myprescription')->__('Email'),
            'class'        => 'required-entry',
            'required'    => true,
            'name'        => 'email',
        ));
        $fieldset->addField('date_of_birth', 'date', array(
            'label'        => Mage::helper('myprescription')->__('DOB'),
            'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'image'  => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'/adminhtml/default/default/images/grid-cal.gif',
            'name'        => 'date_of_birth',
        ));
        $fieldset->addField('nhs_number', 'text', array(
            'label'        => Mage::helper('myprescription')->__('NHS Number'),
            'class'        => 'required-entry',
            'required'    => true,
            'name'        => 'nhs_number',
        ));
        $fieldset->addField('doctor_name', 'text', array(
            'label'        => Mage::helper('myprescription')->__('Doctor Name'),
            'class'        => 'required-entry',
            'required'    => true,
            'name'        => 'doctor_name',
        ));
        $fieldset->addField('doctor_phone', 'text', array(
            'label'        => Mage::helper('myprescription')->__('Doctor Phone'),
            'class'        => 'required-entry',
            'required'    => true,
            'name'        => 'doctor_phone',
        ));
        $fieldset->addField('detail_medical', 'text', array(
            'label'        => Mage::helper('myprescription')->__('Detail Medical'),
            'class'        => 'required-entry',
            'required'    => true,
            'name'        => 'detail_medical',
        ));
        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('myprescription')->__('Select'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'status',
            'onclick' => "",
            'onchange' => "",
            'value'  => '1',
            'values' => array('-1'=>'Please Select..','0' => 'New','1' => 'Completed'),
            'disabled' => false,
            'readonly' => false,
            'after_element_html' => '<small>Change Status for prescripton repeat</small>',
            'tabindex' => 1
        ));

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('myprescription')->__('Register Info');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('myprescription')->__('Register Info');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }
}
