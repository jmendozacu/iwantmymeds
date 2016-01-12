<?php
class Ecommage_Myprescription_Block_Adminhtml_Sales_Order_Renderer_Prescription extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{
    public function render(Varien_Object $row)
    {
        // load the order
        $order = Mage::getModel('sales/order')->load($row->getId());
        $value = 'No';
        // loop for all order items
        foreach ($order->getAllItems() as $item)
        {
            $product = Mage::getModel('catalog/product')->load($item->getProductId())->getData();
            // find product prescription
            if (isset($product['is_prescription_product'])){
                if ($product['is_prescription_product'] == 1){
                    $value = 'Yes';
                }
            }
        }

        return '<span>'.$value.'</span>';

    }

}