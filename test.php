<?php

require 'app/Mage.php';
umask(0);
Mage::app();

Mage::getModel('ecommage_myprescription/observer')->cronJobImportProduct();