<?php

/**
 * RocketWeb
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category  RocketWeb
 * @package   RocketWeb_GoogleBaseFeedGenerator
 * @copyright Copyright (c) 2012 RocketWeb (http://rocketweb.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author    RocketWeb
 */
class RocketWeb_GoogleBaseFeedGenerator_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Checks if a module is enabled or not
     * @param $module_namespace
     * @return bool
     */
    public function isModuleEnabled($module_namespace = null)
    {
        $modules = Mage::getConfig()->getNode('modules')->children();
        $modulesArray = (array)$modules;
        return isset($modulesArray[$module_namespace]) && $modulesArray[$module_namespace]->active == "true";
    }

    /**
     * $string = preg_replace_callback('/\\\\u(\w{4})/', array(Mage::helper('googlebasefeedgenerator'), 'jsonUnescapedUnicodeCallback'), $string);
     * php 5.2 alternative to JSON_UNESCAPED_UNICODE
     *
     * @param $matches
     * @return string
     */
    public function jsonUnescapedUnicodeCallback($matches) {
        return html_entity_decode('&#x' . $matches[1] . ';', ENT_COMPAT, 'UTF-8');
    }

    /**
     * if (extension_loaded('mbstring')) {
     *    $string = preg_replace_callback("/(&#?[a-z0-9]{2,8};)/i", array(Mage::helper('googlebasefeedgenerator'), 'htmlEntitiesToUtf8Callback'), $string);
     * }
     *
     * @param $matches
     * @return string
     */
    public function htmlEntitiesToUtf8Callback($matches) {
        return mb_convert_encoding($matches[1], "UTF-8", "HTML-ENTITIES");
    }

    /**
     * Calculate the quantity increments including minimal sale quantity
     *
     * @param Mage_Catalog_Model_Product $product
     * @return float
     */
    public function getQuantityIcrements(Mage_Catalog_Model_Product $product)
    {
        $qtyIncrements = 1.0;
        if ($product->getStockItem()) {
            if ($product->getStockItem()->getData('min_sale_qty')) {
                $qtyIncrements = $product->getStockItem()->getData('min_sale_qty');
            }

            if ($product->getStockItem()->getData('enable_qty_increments')) {
                if ($qtyIncrements > 1.0) {
                    $qtyIncrementsTmp = $product->getStockItem()->getData('qty_increments');
                    if ($qtyIncrements % $qtyIncrementsTmp != 0) {
                        $nextIncrement = ceil($qtyIncrements / $qtyIncrementsTmp);
                        $qtyIncrements = $nextIncrement * $qtyIncrementsTmp;
                    }
                } else {
                    $qtyIncrements = $product->getStockItem()->getData('qty_increments');
                }
            }
        }
        return $qtyIncrements;
    }
    /**
     * Checks if MSRP is enabled & product has it & price < msrp
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function hasMsrp(Mage_Catalog_Model_Product $product)
    {
        $catalogHelper = Mage::helper('catalog');
        return method_exists($catalogHelper, 'canApplyMsrp')
            && $catalogHelper->canApplyMsrp($product)
            && $product->hasMsrp()
            && ($product->getPrice() < $product->getMsrp());
    }

    /**
     * Returns the memory limit in bytes ??
     * @return float
     */
    public function getMemoryLimit()
    {
        $memory = ini_get('memory_limit');
        if (is_numeric($memory) && $memory <= 0) {
            return memory_get_usage(true) * 1.5;
        }

        if (!is_numeric($memory)) {
            preg_match('/^\s*([0-9.]+)\s*([KMGTPE])B?\s*$/i', $memory, $matches);
            $num = (float)$matches[1];
            switch (strtoupper($matches[2])) {
                case 'E':
                    $num = $num * 1024;
                case 'P':
                    $num = $num * 1024;
                case 'T':
                    $num = $num * 1024;
                case 'G':
                    $num = $num * 1024;
                case 'M':
                    $num = $num * 1024;
                case 'K':
                    $num = $num * 1024;
            }
            $memory = $num;
        }
        return $memory;
    }
}