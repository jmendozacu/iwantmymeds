<?php 

class Ecommage_Prescriptioncheckout_Model_Observer
{

    public function saveCustomData($observer) {
        $event = $observer->getEvent();
        $order = $event->getOrder();
        $customerData = Mage::getSingleton('customer/session')->getCustomer();
        $customer = Mage::getModel('customer/customer')->load($customerData->getId());
        $prescription = unserialize($customer->getPrescriptionAttribute());
        $order->setData('prescription_attribute',$prescription['prescription_id']);
        Mage::getSingleton('checkout/session')->setPrescriptionAttribute('');
    }

    public function removeItem($observer){
        $checkPrescription = Mage::helper('prescriptioncheckout')->checkPrescriptionInCart();
        if($checkPrescription <= 0){
            $customerData = Mage::getSingleton('customer/session')->getCustomer();
            $customer = Mage::getModel('customer/customer')->load($customerData->getId());
            $customer->setPrescriptionAttribute('');
            try{
                $customer->save();
            }catch (Exception $e){
                Mage::log($e->getMessage());
            }
        }
    }

    public function updateItem($observer){
        $checkPrescription = Mage::helper('prescriptioncheckout')->checkPrescriptionInCart();
        if($checkPrescription <= 0){
            $customerData = Mage::getSingleton('customer/session')->getCustomer();
            $customer = Mage::getModel('customer/customer')->load($customerData->getId());
            $customer->setPrescriptionAttribute('');
            try{
                $customer->save();
            }catch (Exception $e){
                Mage::log($e->getMessage());
            }
        }
    }
}
