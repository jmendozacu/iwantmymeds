<?php

$installer = $this;

$installer->startSetup();

//Create table entity

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('ecommage_myprescription_register_repeat')};
  CREATE TABLE IF NOT EXISTS {$this->getTable('ecommage_myprescription_register_repeat')} (
    `id` int(11) unsigned NOT NULL auto_increment,
    `name_and_address` VARCHAR(255) NULL,
    `telephone_number` VARCHAR(255) NULL,
    `mobile` VARCHAR(255) NULL,
    `email` VARCHAR(255) NULL,
    `date_of_birth` VARCHAR(255) NULL,
    `nhs_number` int(11) NULL,
    `doctor_name` VARCHAR(255) NULL,
    `doctor_phone` VARCHAR(255) NULL,
    `detail_medical` VARCHAR(255) NULL,
    `status` smallint(5) NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 ");

$installer->endSetup();

//$installer->installEntities();