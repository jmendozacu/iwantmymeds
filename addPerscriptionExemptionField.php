<?php
require_once 'app/Mage.php';
umask(0);
Mage::app('default');
//     Add the code you want to execute? here:
//    $c = array (
//    'entity_type_id'  => 5,         // 11 is the id of the entity model “sales/order”. This could be different on our system!
//
//    //Look at database-table “eav_entity_type” for the correct ID!
//
//    'attribute_code'  => 'myprescription_exemption',
//    'backend_type'    => 'text',     // MySQL-DataType
//    'frontend_input'  => 'textarea', // Type of the HTML-Form-Field
//    'is_global'       => '1',
//    'is_visible'      => '1',
//    'is_required'     => '0',
//    'is_user_defined' => '0',
//    'frontend_label'  => 'Prescription Exemption'
//);
//$attribute = new Mage_Eav_Model_Entity_Attribute();
//$attribute->loadByCode($c['entity_type_id'], $c['attribute_code'])
//->setStoreId(0)
//->addData($c);
//$attribute->save();

//
//require_once('app/Mage.php');
//Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
//
//$installer = new Mage_Sales_Model_Mysql4_Setup;
//$attribute  = array(
//    'type'          => 'text',
//    'backend_type'  => 'text',
//    'frontend_input' => 'text',
//    'is_user_defined' => true,
//    'label'         => 'Prescription Exemption',
//    'visible'       => true,
//    'required'      => false,
//    'user_defined'  => true,
//    'searchable'    => true,
//    'filterable'    => true,
//    'comparable'    => true
//);
//$installer->addAttribute('order', 'prescription_exemption', $attribute);
//$installer->endSetup();

//
$_order = Mage::getModel("sales/order")->load(145000075);
////$orderExemption = $_order->getMyprescriptionExemption();
$_order->addStatusHistoryComment('This comment is programatically added to last order in this Magento setup');
////$_order->setMyprescriptionExemption="test";
$_order->save();
?>