<?php
    class Ecommage_Myprescription_SearchController extends Mage_core_Controller_Front_Action{
        public function indexAction(){
            $this->loadLayout();
            $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
            $breadcrumbs->addCrumb('home', array(
                                            'label'=>Mage::helper('cms')->__('home'),
                                            'title'=>Mage::helper('cms')->__('Home Page'),
                                            'link'=>Mage::getBaseUrl()));
            $breadcrumbs->addCrumb('my_prescription', array(
                'label'=>$this->__('My Prescription'),
                'title'=>$this->__('My Prescription'),
                'link'=>Mage::getBaseUrl().'my-prescription'));
            $breadcrumbs->addCrumb('search', array(
                'label'=>$this->__('Search'),
                'title'=>$this->__('Search'),
                'link'=>''));

            $this->renderLayout();
        }
        public function layerAction(){
            $block = $this->getLayout()
                ->createBlock('catalog/layer_view')
                ->setTemplate('catalog/layer/view.phtml');
            $this->getResponse()->setBody($block->toHtml());
        }
    }
