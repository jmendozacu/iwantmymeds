<?php


class Magik_Lambysettings_Model_Config_Width
{

    public function toOptionArray()
    {
        return array(
            array(
	            'value' => 'flexible',
	            'label' => Mage::helper('lambysettings')->__('flexible')),
            array(
	            'value' => 'fixed',
	            'label' => Mage::helper('lambysettings')->__('fixed')),
        );
    }

}
