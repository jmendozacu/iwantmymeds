<?php
/**
 * News List admin edit form main tab
 *
 * @author Magento
 */
class Ecommage_Prescriptioncheckout_Block_Adminhtml_Prescription_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    protected function _prepareForm()
    {
        $model = Mage::helper('prescriptioncheckout')->getNewsItemInstance();
        if (Mage::helper('prescriptioncheckout')->isActionAllowed('save')) {
            $isElementDisabled = false;
        }else {
            $isElementDisabled = true;
        }

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('news_main_');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('prescriptioncheckout')->__('Prescription Item Info')
        ));

        if ($model->getId()) {
            $fieldset->addField('prescription_id', 'hidden', array(
                'name' => 'prescription_id',
            ));
        }
        $fieldset->addField('title', 'text', array(
            'name'     => 'title',
            'label'    => Mage::helper('prescriptioncheckout')->__('Title'),
            'title'    => Mage::helper('prescriptioncheckout')->__('Title'),
            'required' => true,
            'disabled' => $isElementDisabled
        ));

        $fieldset->addField('descriptions', 'textarea', array(
            'name'     => 'descriptions',
            'label'    => Mage::helper('prescriptioncheckout')->__('Descriptions'),
            'title'    => Mage::helper('prescriptioncheckout')->__('Descriptions'),
            'required' => true,
            'disabled' => $isElementDisabled
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
        return Mage::helper('prescriptioncheckout')->__('Prescription Info');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('prescriptioncheckout')->__('Prescription Info');
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
