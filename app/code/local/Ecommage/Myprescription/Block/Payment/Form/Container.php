<?php
class Ecommage_Myprescription_Block_Payment_Form_Container extends  Mage_Checkout_Block_Onepage_Payment_Methods{
    /**
     * Retrieve available payment methods
     *
     * @return array
     */
    public function getMethods()
    {
       $methods = $this->getData('methods');
        if ($methods === null) {
            $quote = $this->getQuote();
            $store = $quote ? $quote->getStoreId() : null;
            $methods = array();
            foreach ($this->helper('payment')->getStoreMethods($store, $quote) as $method) {
                if ($this->_canUseMethod($method)) {
                    $this->_assignMethod($method);
                    $methods[] = $method;
                }
            }
            $this->setData('methods', $methods);
        }
        return $methods;
    }

}