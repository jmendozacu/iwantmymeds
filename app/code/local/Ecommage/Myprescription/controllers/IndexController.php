<?php
class Ecommage_Myprescription_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $block = $this->getLayout()
            ->createBlock('myprescription/formsearch')
            ->setTemplate('myprescription/formsearch.phtml');
        $param = '';
        if (isset($_GET['param'])) {
            $param = $_GET['param'];
        }
        $html = '<div class="body-search">';
        $html .=     '<div class="title">';
        $html .=        '<h1>';
            if($param == 1){
                $html .=  "link Electronic";
            }else if($param == 2){
                $html .=  "NHS Prescription Order";
            }else if($param == 3){
                $html .=  "link Private";
                $html .=  "Private Prescription";
            }else if($param == 4){
                $html .= "link Repeat";
            }else if($param == 5){
                $html .= "link Vet";
            }
        $html .=        '</h1>';
        $html .=     '</div>';
        $html .=     '<div class="warning"></div>';
        $html .=    $block->toHtml();
        $html .= '</div>';

        print($html);
    }

    public function prescriptioncheckAction(){
        $data = $this->getRequest()->getParams();
        $result = array();
        if(!empty($data)){
            Mage::getSingleton("core/session")->setPerscriptionCheck($data['perscription_type']);
            $result['success'] = true;
            $result['perscription_type'] = $data['perscription_type'];
            $result['url'] = $data['url_redirect_product'];
        }else{
            $result['success'] = false;
        }

        $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($result));
    }
}
