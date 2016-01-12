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
class RocketWeb_GoogleBaseFeedGenerator_Model_Map_Product_Grouped extends RocketWeb_GoogleBaseFeedGenerator_Model_Map_Product_Abstract
{
    /**
     * Support for configurable items product option not yet implemented
     * @return bool
     */
    protected function _isAllowProductOptions()
    {
        return false;
    }

    /**
     * @return int
     */
    public function getChildrenCount() {
        return (count($this->getAssocIds()));
    }

    /**
     * Iterate through associated products and set mapping objects
     *
     * @return $this
     */
    public function _beforeMap()
    {
        $this->_assocs = array();
        $stockStatus = $this->getConfig()->getOutOfStockStatus();

        foreach ($this->getAssocIds() as $assocId) {

            $assoc = Mage::getModel('catalog/product');
            $assoc->setStoreId($this->getStoreId());
            $assoc->getResource()->load($assoc, $assocId);
            $assoc->setData('quantity', 0);

            if ($this->getGenerator()->getData('verbose')) {
                echo $this->getGenerator()->formatMemory(memory_get_usage(true)) . " - Grouped associated SKU " . $assoc->getSku() . ", ID " . $assoc->getEntityId() . "\n";
            }

            $stock = $this->getConfig()->getOutOfStockStatus();

            if (!$this->getConfigVar('use_default_stock', 'columns')) {
                $stock_attribute = $this->getGenerator()->getAttribute($this->getConfigVar('stock_attribute_code', 'columns'));
                if ($stock_attribute === false) {
                    Mage::throwException(sprintf('Invalid attribute for Availability column. Please make sure proper attribute is set under the setting "Alternate Stock/Availability Attribute.". Provided attribute code \'%s\' could not be found.', $this->getConfigVar('stock_attribute_code', 'columns')));
                }

                $stock = trim(strtolower($this->getAttributeValue($assoc, $stock_attribute)));
                if (array_search($stock, $this->getConfig()->getAllowedStockStatuses()) === false) {
                    $stock = $this->getConfig()->getOutOfStockStatus();
                }
            } else {
                $stockItem = Mage::getModel('cataloginventory/stock_item');
                $stockItem->setStoreId($this->getStoreId());
                $stockItem->getResource()->loadByProductId($stockItem, $assoc->getId());
                $stockItem->setOrigData();

                if ($stockItem->getId() && $stockItem->getIsInStock()) {
                    $assoc->setData('quantity', $stockItem->getQty());
                    $stock = $this->getConfig()->getInStockStatus();
                }

                // Clear stockItem memory
                unset($stockItem->_data);
                $this->getTools()->clearNestedObject($stockItem);
            }

            // Append assoc considering the appropriate stock status
            if ($this->getConfigVar('add_out_of_stock', 'grouped_products') || $stock == $this->getConfig()->getInStockStatus()) {
                $this->_assocs[$assocId] = $assoc;
            } else {
                // Set skip messages
                if ($this->getConfigVar('log_skip')) {
                    $this->log(sprintf("product id %d sku %s, grouped associated, skipped - out of stock", $assocId, $assoc->getSku()));
                }
            }

            // Set stock status of the current item and check if the status has changed
            if ($stock == $this->getConfig()->getInStockStatus()) {
                $stockStatus = $stock;
            }
        }

        // Set grouped stock status if all assocs have the same stock status, only for default stocks
        if ($this->getConfigVar('use_default_stock', 'columns')) {
            $this->setAssociatedStockStatus($stockStatus);
            if ($stockStatus == $this->getConfig()->getOutOfStockStatus() && !$this->getConfigVar('add_out_of_stock', 'filters')) {
                $this->setSkip(sprintf("product id %d sku %s, grouped, skipped - out of stock.", $this->getProduct()->getId(), $this->getProduct()->getSku()));
            }
        }

        return parent::_beforeMap();
    }

    /**
     * Builds the associated maps from assocs array
     *
     * @return $this
     */
    protected function _setAssocMaps()
    {
        $assocMapArr = array();
        foreach ($this->getProduct()->getTypeInstance()->getAssociatedProducts() as $assoc) {
            $assocMap = $this->_getAssocMapModel($assoc);
            $assocId = $assoc->getEntityId();
            if (!in_array($assocId, $this->_assoc_ids) || $assocMap->checkSkipSubmission(true, 'grouped')) {
                unset($this->_assocs[$assocId]);
                continue;
            }
            $assocMapArr[$assocId] = $assocMap;
        }
        $this->setAssocMaps($assocMapArr);

        if (count($assocMapArr) <= 0) {
            $this->setSkip(sprintf("product id %d product sku %s, skipped - All associated products of the grouped product are disabled or out of stock.", $this->getProduct()->getId(), $this->getProduct()->getSku()));
        }
        return $this;
    }

    /**
     * @return array
     */
    public function map()
    {
        $rows = array();
        $this->_beforeMap();

        if ($this->getConfig()->isAllowGroupedMode($this->getStoreId())) {
            if (!$this->isSkip()) {

                // simulate parent::map() without clearing associated_maps from memory, as associated more could be on.
                $row = parent::_map();
                reset($row); $row = current($row);
                $this->_checkEmptyColumns($row);

                if (!$this->isSkip()) {
                    $rows[] = $row;
                }
            }
        }

        if ($this->getConfig()->isAllowGroupedAssociatedMode($this->getStoreId())) {
            foreach ($this->getAssocMaps() as $assocMap) {

                $row = $assocMap->map();
                reset($row); $row = current($row);

                if (!$assocMap->isSkip()) {
                    $rows[] = $row;
                }
            }
        }

        // if any of the associated not skipped, force add them to the feed
        if (count($rows)) {
            $this->unSkip();
        }

        return $this->_afterMap($rows);
    }

    /**
     * @param $rows
     * @return array
     */
    public function _afterMap($rows)
    {
        // Free some memory
        foreach ($this->_assocs as $assoc) {
            $this->getTools()->clearNestedObject($assoc);
        }
        return parent::_afterMap($rows);
    }

    /**
     * Computes prices for given or current product.
     * It returns an array of 4 prices: price and special_price, buth including and excluding tax
     *
     * @return mixed
     */
    public function getPrices()
    {
        if ($this->hasData('price_array')) {
            return $this->getData('price_array');
        }

        $prices = array('p_excl_tax' => 0, 'p_incl_tax' => 0, 'sp_excl_tax' => 0, 'sp_incl_tax' => 0);

        if (!$this->hasAssocMaps()) {
            return $prices;
        }

        switch ($this->getConfigVar('price_display_mode', 'grouped_products')) {

            case RocketWeb_GoogleBaseFeedGenerator_Model_Source_Product_Grouped_Price::PRICE_SUM_DEFAULT_QTY :
                foreach ($this->getAssocMaps() as $assocMap) {
                    $qty = $assocMap->getProduct()->getQty();
                    $qty = $qty > 0 ? $qty : 1;
                    $p_asoc = $assocMap->getPrices();
                    $prices['p_excl_tax']  += $p_asoc['p_excl_tax'] * $qty;
                    $prices['p_incl_tax']  += $p_asoc['p_incl_tax'] * $qty;
                    $prices['sp_excl_tax'] += $p_asoc['sp_excl_tax'] * $qty;
                    $prices['sp_incl_tax'] += $p_asoc['sp_incl_tax'] * $qty;
                }
                break;
            case RocketWeb_GoogleBaseFeedGenerator_Model_Source_Product_Grouped_Price::PRICE_START_AT:
                $minAssocMap = $this->getMinPriceAssocMap();
                if ($minAssocMap) {
                    $prices = $minAssocMap->getPrices();
                }
                break;
        }

        $this->setData('price_array', $prices);
        return $this->getData('price_array');
    }


    /**
     * @param bool|true $rules
     * @param null $product
     * @return bool
     */
    public function hasSpecialPrice($rules = true, $product = null)
    {
        $has = false;
        $display_mode = $this->getConfigVar('price_display_mode', 'grouped_products');

        if ($this->_hasDefaultQty() && $display_mode == RocketWeb_GoogleBaseFeedGenerator_Model_Source_Product_Grouped_Price::PRICE_SUM_DEFAULT_QTY) {
            foreach ($this->getAssocMaps() as $assocMap) {
                $has = $assocMap->hasSpecialPrice($rules, $product);
                if ($has && $assocMap->getProduct()->getQty() > 0) {
                    break;
                }
            }
        } else { // RocketWeb_GoogleBaseFeedGenerator_Model_Source_Product_Grouped_Price::PRICE_START_AT
            $minAssocMap = $this->getMinPriceAssocMap();
            if ($minAssocMap) {
                $has = $minAssocMap->hasSpecialPrice($rules, $product);
            }
        }

        return $has;
    }

    /**
     * @param array $params
     * @return string
     */
    protected function mapDirectiveSalePriceEffectiveDate($params = array())
    {
        if (!$this->hasSpecialPrice()) {
            return '';
        }

        $display_mode = $this->getConfigVar('price_display_mode', 'grouped_products');
        if ($this->_hasDefaultQty()
            && $display_mode == RocketWeb_GoogleBaseFeedGenerator_Model_Source_Product_Grouped_Price::PRICE_SUM_DEFAULT_QTY
        ) {
            //get min interval from all associated products
            $start = $end = null;
            foreach ($this->getAssocMaps() as $assocMap) {
                if ($assocMap->getProduct()->getQty() > 0) {
                    $dates = $assocMap->_getSalePriceEffectiveDates();
                    if (!empty($dates)) {
                        if (empty($start) || $start < $dates['start']) {
                            $start = $dates['start'];
                        }
                        if (empty($end) || $end > $dates['end']) {
                            $end = $dates['end'];
                        }
                    }
                }
            }
            $cell = $this->formatDateInterval(array('start' => $start, 'end' => $end));
        } else {
            $minAssocMap = $this->getMinPriceAssocMap();
            if ($minAssocMap) {
                $cell = $minAssocMap->mapDirectiveSalePriceEffectiveDate($params);
            }
        }

        return $cell ? $cell : '';
    }

    /**
     * @return mixed
     */
    public function getMinPriceAssocMap()
    {
        if ($this->hasData('min_price_assoc_map')) {
            return $this->getData('min_price_assoc_map');
        }

        $min_price = PHP_INT_MAX;

        if ($this->hasAssocMaps()) {
            foreach ($this->getAssocMaps() as $assocMap) {
                $prices = $assocMap->getPrices();
                $price = $assocMap->hasSpecialPrice() ? $prices['sp_excl_tax'] : $prices['p_excl_tax'];
                if ($price > 0 && $min_price > $price) {
                    $min_price = $price;
                    $this->setData('min_price_assoc_map', $assocMap);
                }
            }
        }

        return $this->getData('min_price_assoc_map');
    }

    /**
     * @return bool
     */
    protected function _hasDefaultQty()
    {
        $has = false;
        if ($this->hasAssocMaps()) {
            foreach ($this->getAssocMaps() as $assocMap) {
                if ($assocMap->getProduct()->getQty() > 0) {
                    $has = true;
                    break;
                }
            }
        }
        return $has;
    }

    /**
     * @param array $params
     * @return string
     */
    protected function mapDirectiveQuantity($params = array())
    {
        $cell = $this->getInventoryCount();

        // If Qty not set at parent item, summarize it from associated items
        if ($params['map']['param'] == RocketWeb_GoogleBaseFeedGenerator_Model_Source_Directive_Product_Quantity::ITEM_SUM_DEFAULT_QTY) {
            $qty = 0;
            foreach ($this->_assocs as $assoc) {
                $qty += $assoc->getData('quantity');
            }
            $cell = $qty ? $qty : $cell;
        }

        $cell = sprintf('%d', $cell);
        $this->_findAndReplace($cell, $params['map']['column']);
        return $cell;
    }

    /**
     * Array with associated products ids in current store.
     *
     * @return array
     */
    public function getAssocIds()
    {
        if (is_null($this->_assoc_ids)) {
            $this->_assoc_ids = $this->loadAssocIds($this->getProduct(), $this->getStoreId());
        }
        return $this->_assoc_ids;
    }

    /**
     * @param array $params
     * @return string
     */
    protected function mapDirectiveAvailability($params = array())
    {
        $grouped_status = parent::mapDirectiveAvailability($params);

        if ($this->hasAssociatedStockStatus()
            && $grouped_status == $this->getConfig()->getInStockStatus()
            && $this->getAssociatedStockStatus() == $this->getConfig()->getInStockStatus()) {
            return $this->getConfig()->getInStockStatus();
        }

        return $this->cleanField($grouped_status, $params);
    }
}