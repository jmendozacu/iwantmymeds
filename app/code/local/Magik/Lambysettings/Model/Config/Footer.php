<?php


class Magik_Lambysettings_Model_Config_Footer
{

    public function toOptionArray()
    {
        return array(
            array(
	            'value'=>'simple',
	            'label' => Mage::helper('lambysettings')->__('simple')),
            array(
	            'value'=>'informative',
	            'label' => Mage::helper('lambysettings')->__('informative')),
        );
    }

}
