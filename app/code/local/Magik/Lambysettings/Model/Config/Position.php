<?php


class Magik_Lambysettings_Model_Config_Position
{

    public function toOptionArray()
    {
        return array(
            array(
	            'value'=>'top-left',
	            'label' => Mage::helper('lambysettings')->__('Top Left')),
            array(
	            'value'=>'top-right',
	            'label' => Mage::helper('lambysettings')->__('Top Right')),                       

        );
    }

}
