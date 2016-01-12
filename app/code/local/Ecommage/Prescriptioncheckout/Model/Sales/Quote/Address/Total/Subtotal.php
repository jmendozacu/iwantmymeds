<?php
class Ecommage_Prescriptioncheckout_Model_Sales_Quote_Address_Total_Subtotal extends Mage_Sales_Model_Quote_Address_Total_Subtotal
{
    protected function _initItem($address, $item)
    {
        if ($item instanceof Mage_Sales_Model_Quote_Address_Item) {
            $quoteItem = $item->getAddress()->getQuote()->getItemById($item->getQuoteItemId());
        }
        else {
            $quoteItem = $item;
        }
        $product = $quoteItem->getProduct();
        $product->setCustomerGroupId($quoteItem->getQuote()->getCustomerGroupId());

        //check is prescription product
        $modelProduct = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('is_prescription_product')
            ->addFieldToFilter('is_prescription_product', array("eq"=>1))
            ->addFieldToFilter('entity_id', array("eq"=>$product->getId()));
        $productData = $modelProduct->getData();
        $customerData = Mage::getSingleton('customer/session')->getCustomer();
        $customer = Mage::getModel('customer/customer')->load($customerData->getId());
        $prescription = unserialize($customer->getPrescriptionAttribute());
        /**
         * Quote super mode flag mean what we work with quote without restriction
         */
        if ($item->getQuote()->getIsSuperMode()) {
            if (!$product) {
                return false;
            }
        }
        else {
            if (!$product || !$product->isVisibleInCatalog()) {
                return false;
            }
        }

        if ($quoteItem->getParentItem() && $quoteItem->isChildrenCalculated()) {
            $finalPrice = $quoteItem->getParentItem()->getProduct()->getPriceModel()->getChildFinalPrice(
                $quoteItem->getParentItem()->getProduct(),
                $quoteItem->getParentItem()->getQty(),
                $quoteItem->getProduct(),
                $quoteItem->getQty()
            );
            if($productData !=null && $prescription !=null){
                $finalPrice = 0;
            }
            $item->setPrice($finalPrice)
                ->setBaseOriginalPrice($finalPrice);
            $item->calcRowTotal();
        } else if (!$quoteItem->getParentItem()) {
            $finalPrice = $product->getFinalPrice($quoteItem->getQty());
            if($productData !=null && $prescription !=null){
                $finalPrice = 0;
            }
            $item->setPrice($finalPrice)
                ->setBaseOriginalPrice($finalPrice);
            $item->calcRowTotal();
            $this->_addAmount($item->getRowTotal());
            $this->_addBaseAmount($item->getBaseRowTotal());
            $address->setTotalQty($address->getTotalQty() + $item->getQty());
        }

        return true;
    }
}
		