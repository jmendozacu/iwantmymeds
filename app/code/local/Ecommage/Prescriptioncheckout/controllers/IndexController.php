<?php
class Ecommage_Prescriptioncheckout_IndexController extends  Mage_Core_Controller_Front_Action
{
    public function cartAction(){
//        need to work here to set the exemption code in the session.
        $data = $this->getRequest()->getParams();
        $result = array();
        if(!empty($data)){
            Mage::getSingleton("core/session")->setPrescriptionExemptionCheck($data['exemption_type']);
            $result['success'] = true;
            $result['exemption_type'] = $data['exemption_type'];
            $result['url'] = $data['url_redirect_product'];
        }else{
            $result['success'] = false;
        }
        $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($result));

//        try {
//
//            $data = $this->getRequest()->getParam('prescription');
//            $model = Mage::getModel('prescriptioncheckout/prescription')->load($data);
//            $data_model = $model->getData();
//            $data['descriptions'] = $data_model['descriptions'];
//            $data['title'] = $data_model['title'];
//            $result = array();
//
//            if(!empty($model)){
//                Mage::getSingleton("core/session")->setCartAction($data_model);
//                $result['success'] = true;
//                $result['descriptions'] = $data['descriptions'];
//                $result['title'] =  $data['title'];
//            }else{
//                $result['success'] = false;
//            }
//
////
////            if(Mage::getSingleton('customer/session')->isLoggedIn()) {
////                $customerData = Mage::getSingleton('customer/session')->getCustomer();
////                $customer = Mage::getModel('customer/customer')->load($customerData->getId());
////                $data = unserialize($customer->getPrescriptionAttribute());
////                $data_model = $model->getData();
////                $data['prescription_id'] = $data_model['prescription_id'];
////                $data['descriptions'] = $data_model['descriptions'];
////                $data['title'] = $data_model['title'];
////                $customer->setPrescriptionAttribute(serialize($data));
////                try{
////                    $customer->save();
////                }catch (Exception $e){
////                    Mage::log($e->getMessage());
////                }
////            }
//            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(true));
//            // redirect to link onpage checkout
//            if ($this->getRequest()->getParam('redr') == 1) {
//                if($this->getRequest()->getParam('url_product') != ""){
//                    $url = $this->getRequest()->getParam('url_product');
//                }else{
//                    $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'onepage';
//                }
//                Mage::app()->getResponse()->setRedirect($url)->sendResponse();
//            }
//        }catch (Exception $e){
//            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(false));
//        }
    }
}
