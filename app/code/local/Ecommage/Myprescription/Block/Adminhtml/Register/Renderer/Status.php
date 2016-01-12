<?php
class Ecommage_Myprescription_Block_Adminhtml_Register_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{
    public function render(Varien_Object $row)
    {
        $value =  '';
        if($row->getData($this->getColumn()->getIndex()) == 1){
            $value = 'Complete';

        }else{
            $value = 'New';
        }
        return '<span>'.$value.'</span>';

    }

}