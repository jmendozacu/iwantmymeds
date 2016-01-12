<?php
$installer = $this;
$installer->startSetup();
$installer->addAttribute("order", "prescription_attribute", array("type"=>"text"));
$installer->addAttribute("quote", "prescription_attribute", array("type"=>"text"));
$installer->endSetup();
