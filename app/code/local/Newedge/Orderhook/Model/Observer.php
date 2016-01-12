<?php

class Newedge_Orderhook_Model_Observer
{
    public function implementOrderStatus($event){
        $order = $event->getOrder();

//        check if the order has a prescription item on it.
        if ($this->_hasPrescriptionItem($order)){
            $this->_updateOrderStatus($order);
        }
        return $this;
    }

    public function logMessage($message){
        $fh = fopen(Mage::getBaseDir('var') . DS . 'log'. DS . 'orderstatus', "a");
        fwrite($fh, "\r\n\r\n");
        fwrite($fh, $message . "\r\n");
        fclose($fh);
    }

    private function _hasPrescriptionItem($order){
//        get order items
        $orderItems = $order->getAllVisibleItems();
        $isPrescriptionOrder = false;
        foreach($orderItems as $item){
//            check if $item is prescription
            $productId = $item->getProductId();
            $product = Mage::getModel('catalog/product')->load($productId);
            $isPrescription = $product->getData("is_prescription_product");
            if ($isPrescription){
                $isPrescriptionOrder = true;
            }
        }
        return $isPrescriptionOrder;
    }

    private function _updateOrderStatus($order){
        $order->setStatus("awaiting_authorization", true);
        $order->save();
    }
}