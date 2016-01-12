<?php
    class Ecommage_Myprescription_RegisterrepeatController extends Mage_core_Controller_Front_Action{
        public function indexAction(){
            $block = $this->getLayout()
                ->createBlock('myprescription/rigisterrepeat')
                ->setTemplate('myprescription/registerform.phtml');
            $this->getResponse()->setBody($block->toHtml());
        }
        public function saveAction(){
            $model = Mage::getModel('ecommage_myprescription/myprescription');
            $data = $this->getRequest()->getPost();
            // print_r($abc);
            $model->addData($data);
            try {
                $hasError = false;
                $model->save();
                $this->_redirect('my-prescription/thank-you-repeat');
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addSuccess(Mage::helper('myprescription')->__('Error could not save register.'));
            }
        }
    }