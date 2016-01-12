<?php
$installer = $this;
$installer->startSetup();
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('ecommage_prescription')};
CREATE TABLE {$this->getTable('ecommage_prescription')} (
  prescription_id int(10) unsigned NOT NULL auto_increment,
  descriptions varchar(255) NOT NULL default '',
  title varchar(255) NOT NULL default '',
  PRIMARY KEY(`prescription_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
   ");
$installer->endSetup();