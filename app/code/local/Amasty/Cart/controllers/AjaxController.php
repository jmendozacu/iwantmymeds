<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2013 Amasty (http://www.amasty.com)
* @package Amasty_Cart
*/
class Amasty_Cart_AjaxController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $idProduct = Mage::app()->getRequest()->getParam('product_id');
        $IsProductView = Mage::app()->getRequest()->getParam('IsProductView');
        $params = Mage::app()->getRequest()->getParams();
        unset($params['product_id']);
        unset($params['IsProductView']);
        $product = Mage::getModel('catalog/product')
                   ->setStoreId(Mage::app()->getStore()->getId())
                   ->load($idProduct);
        $responseText = '';
        if ($product->getId())
        {
            try{
                if(($product->getTypeId() == 'simple' && !($product->getRequiredOptions() || (Mage::getStoreConfig('amcart/general/display_options') && $product->getHasOptions()))) || count($params) > 0 || ($product->getTypeId() == 'virtual' && !($product->getRequiredOptions() || (Mage::getStoreConfig('amcart/general/display_options') && $product->getHasOptions())))){
                    if(!array_key_exists('qty', $params)) {
                        $params['qty'] = $product->getStockItem()->getMinSaleQty();  
                    }  
                    $cart = Mage::getSingleton('checkout/cart');
                    $cart->addProduct($product, $params);
                    $cart->save();
                    Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
                    if (!$cart->getQuote()->getHasError()){
                        $responseText = $this->addToCartResponse($product, $cart, $IsProductView, $params,0);    
                    }    
                }
                else{
                     $responseText = $this->showOptionsResponse($product, $IsProductView);    
                }
                    
            }
            catch (Exception $e) {
                $responseText = $this->addToCartResponse($product, $cart, $IsProductView, $params, $e->getMessage());
                Mage::logException($e);
            }
        }
        $this->getResponse()->setBody($responseText);
    }
    
    //creating options popup 
    private function showOptionsResponse($product, $IsProductView){
        Mage::register('current_product', $product);                  
        Mage::register('product', $product);                  
        $block = Mage::app()->getLayout()->createBlock('catalog/product_view', 'catalog.product_view');
        $textScript = (Mage::getStoreConfig('amconf/list/enable_list') && 'true' == (string)Mage::getConfig()->getNode('modules/Amasty_Conf/active') && !$IsProductView)? ' optionsPrice['.$product->getId().'] = new Product.OptionsPrice('.$block->getJsonConfig().');': '';
        $html = '<script type="text/javascript">
                    optionsPrice = new Product.OptionsPrice('.$block->getJsonConfig().'); 
                    '.$textScript.'  
                    $("messageBox").addClassName("amcart-options"); 
                 </script><form id="product_addtocart_form" enctype="multipart/form-data">';
        $js = Mage::app()->getLayout()->createBlock('core/template', 'product_js')
                            ->setTemplate('catalog/product/view/options/js.phtml');
        $js->setProduct($product);
        $html .= $js->toHtml();
        $options = Mage::app()->getLayout()->createBlock('catalog/product_view_options', 'product_options')
                            ->setTemplate('catalog/product/view/options.phtml')
                            ->addOptionRenderer('text', 'catalog/product_view_options_type_text', 'catalog/product/view/options/type/text.phtml')
                            ->addOptionRenderer('select', 'catalog/product_view_options_type_select', 'catalog/product/view/options/type/select.phtml')
                            ->addOptionRenderer('file', 'catalog/product_view_options_type_file', 'catalog/product/view/options/type/file.phtml')
                            ->addOptionRenderer('date', 'catalog/product_view_options_type_date', 'catalog/product/view/options/type/date.phtml');
        $options->setProduct($product);
        $html .= $options->toHtml();                                            
         
        if ($product->isConfigurable())
        {
            $configurable = Mage::app()->getLayout()->createBlock('catalog/product_view_type_configurable', 'product_configurable_options');
            //if Colors Swatches Pro
            if('true' == (string)Mage::getConfig()->getNode('modules/Amasty_Conf/active') && Mage::getStoreConfig('amconf/list/enable_list') &&  !$IsProductView){
                $configurable->setTemplate('amconf/configurable.phtml');
            }
            else{
                $configurable ->setTemplate('catalog/product/view/type/options/configurable.phtml');    
            }
            $configurableData = Mage::app()->getLayout()->createBlock('catalog/product_view_type_configurable', 'product_type_data')
                            ->setTemplate('catalog/product/view/type/configurable.phtml');
            $configurable->setProduct($product);
            $configurableData->setProduct($product);
            $htmlCong = $configurable->toHtml();
            $html .= $htmlCong.$configurableData->toHtml();
        }
		if($product->isGrouped()){
              $blockGr = Mage::app()->getLayout()->createBlock('catalog/product_view_type_grouped', 'catalog.product_view_type_grouped')
                                                 ->setTemplate('catalog/product/view/type/grouped.phtml'); 
              $html .= $blockGr->toHtml();                                                                             
        }
         
        if ($product->getTypeId() == 'downloadable')
        {
            $downloadable = Mage::app()->getLayout()->createBlock('downloadable/catalog_product_links', 'product_downloadable_options')
                            ->setTemplate('downloadable/catalog/product/links.phtml');
           $html .= $downloadable->toHtml();
       }
       if($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE){
                 $blockBn = Mage::app()->getLayout()->createBlock('bundle/catalog_product_view_type_bundle', 'product.info.bundle.options') ;                                           
                 $blockBn ->addRenderer('select', 'bundle/catalog_product_view_type_bundle_option_select');
                 $blockBn->addRenderer('multi', 'bundle/catalog_product_view_type_bundle_option_multi');
                 $blockBn->addRenderer('radio', 'bundle/catalog_product_view_type_bundle_option_radio', 'bundle/catalog/product/view/type/bundle/option/radio.phtml');
                 $blockBn->addRenderer('checkbox', 'bundle/catalog_product_view_type_bundle_option_checkbox', 'bundle/catalog/product/view/type/bundle/option/checkbox.phtml');
                 $blockBn->setTemplate('bundle/catalog/product/view/type/bundle/options.phtml');
                 $html .= $blockBn->toHtml();
                 $blockBn->setTemplate('bundle/catalog/product/view/type/bundle.phtml');
                 $html .= $blockBn->toHtml();
       }
       else{
            $price = Mage::app()->getLayout()->createBlock('catalog/product_view', 'product_view')
                                ->setTemplate('catalog/product/view/price_clone.phtml');
            $html .= $price->toHtml();    
       }
          
        
        $html .= '</form>';
        $result = array(
              'title'     =>  $this->__('Set options'), 
              'message'   =>  $html, 
              'b1_name'   =>  $this->__('Add to cart'), 
              'b2_name'   =>  $this->__('Cancel'), 
              'b1_action' =>  'AmAjaxObj.sendAjax('.$product->getId().', 1);', 
              'b2_action' =>  '$.confirm.hide();', 
              'align' =>  '$.confirm.hide();' 
          );
         $result = $this->replaceJs($result);
         return Zend_Json::encode($result);    
    } 
   
   //reload my cart
    public function cartAction()
    {   
        $_SERVER['REQUEST_URI'] = str_replace(Mage::getBaseUrl(), '/index.php/', $_SERVER['HTTP_REFERER']);
        $myCart = Mage::app()->getLayout()->createBlock('checkout/cart_sidebar', 'cart_sidebar')
                             ->setTemplate('checkout/cart/sidebar.phtml');
        $this->getResponse()->setBody($myCart->toHtml());
    }
    
    //reload minicart
    public function minicartAction()
    {   
        $_SERVER['REQUEST_URI'] = str_replace(Mage::getBaseUrl(), '/index.php/', $_SERVER['HTTP_REFERER']);
        $myCart = Mage::app()->getLayout()->createBlock('checkout/cart_sidebar', 'cart_sidebar')
                             ->setTemplate('amcart/checkout/cart/mini_cart.phtml');
        $this->getResponse()->setBody($myCart->toHtml());
    }
    
    //reload count
    public function countAction()
    {
        $block = Mage::app()->getLayout()->createBlock('amcart/config', 'amcart.config');
        if (Mage::getSingleton('checkout/cart')->getSummaryQty() == 1){
             $html = $this->__('There is <a href="%s" id="am-a-count">1 item</a> in your cart.', $block->getUrl('checkout/cart'));    
        }
        else{
             $html = $this->__('There are <a href="%s" id="am-a-count">%s items</a> in your cart.', $block->getUrl('checkout/cart'), Mage::getSingleton('checkout/cart')->getSummaryQty());    
        }
        $this->getResponse()->setBody($html);
    }
    
    //creating finale popup 
    private function addToCartResponse($product, $cart, $IsProductView, $params, $text){
       $result = array(
                  'title'     =>  $this->__('Information'), 
                  'message'   =>  $this->__('<p>You have added product to cart.</p>'), 
                  'b1_name'   =>  $this->__('View cart'), 
                  'b2_name'   =>  $this->__('Continue'), 
                  'count'     =>  Mage::getSingleton('checkout/cart')->getSummaryQty()>1?' (' . Mage::getSingleton('checkout/cart')->getSummaryQty(). $this->__(' items)'):' (' . Mage::getSingleton('checkout/cart')->getSummaryQty(). $this->__(' item)'),
                  'b1_action' =>  'document.location = "'.Mage::helper('checkout/cart')->getCartUrl().'";', 
                  'b2_action' =>  '$.confirm.hide();'
        );
        if(Mage::registry('current_category')){
            Mage::register('am_current_category', Mage::registry('current_category'));
        }
        if ($IsProductView &&  Mage::helper('amcart')->getProductButton() && Mage::registry('current_category')){
             $result['b2_action'] =  'document.location = "'.Mage::registry('current_category')->getUrl().'";';
        }
        if($text){
             $result['message'] = '<p>' . $text . '</p>';
        }
        else{
            Mage::unregister('current_product');
            Mage::unregister('product');
            Mage::register('current_product', $product);                  
            Mage::register('product', $product);
              
            $block = Mage::app()->getLayout()->createBlock('amcart/config', 'amcart.config');
            if(Mage::helper('amcart')->displayProduct()){
               $block->setTemplate('amcart/catalog/product/view/dialog.phtml');
               //setting simple/configurable product
               if($product->getTypeId() == "configurable" && (Mage::getStoreConfig('amcart/configurable/image') || Mage::getStoreConfig('amcart/configurable/name'))){  
                    $simpleProduct = $product->getTypeInstance()->getProductByAttributes($params['super_attribute']); 
                    Mage::register('simpleProduct', $simpleProduct->getEntityId()); 
                } 
                
               $block->setProduct($product);
               $result['message'] = $block->toHtml();    
            }
            //display count cart item
            if(Mage::helper('amcart')->displayCount()){
                 if (Mage::getSingleton('checkout/cart')->getSummaryQty() == 1){
                     $result['message'] .= $this->__('<div id="amcart-count">There is <a href="%s" id="am-a-count">1 item</a> in your cart.</div>', $block->getUrl('checkout/cart')); 
                 }
                 else{
                    $result['message'] .= $this->__('<div id="amcart-count">There are <a href="%s" id="am-a-count">%s items</a> in your cart.</div>', $block->getUrl('checkout/cart'), Mage::getSingleton('checkout/cart')->getSummaryQty());    
                 }
            }
            //display summ price
            if(Mage::helper('amcart')->displaySumm()){
                 $result['message'] .= $this->__('<p>Cart Subtotal: ') . Mage::helper('checkout')->formatPrice($this->getSubtotal($cart)); 
		   if ($_subtotalInclTax = $this->getSubtotalInclTax($cart)){
                        $result['message'] .= '<br />(' . Mage::helper('checkout')->formatPrice($_subtotalInclTax) .' ' . Mage::helper('tax')->getIncExcText(true). ')';
                 }
 		 $result['message'] .='</p>';
            }
            
            //display related products
            if(Mage::getStoreConfig('amcart/selling/related')){
                $relBlock = Mage::app()->getLayout()->createBlock('catalog/product_list_related', 'product_list_related')
                                ->setTemplate('amcart/catalog/product/list/related.phtml');
                $relBlock->setProduct($product);
                $result['message'] .= $relBlock->toHtml();   
            }
        }
        //addd timer
        if(0 < Mage::helper('amcart')->getTime()){
            $result['b2_name'] .= '(' . Mage::helper('amcart')->getTime() . ')';
        }
        $result = $this->replaceJs($result);
        return Zend_Json::encode($result);
        
    }
    
    public function getSubtotal($cart, $skipTax = true)
    {
        $subtotal = 0;
        $totals = $cart->getQuote()->getTotals();
        $config = Mage::getSingleton('tax/config');
        if (isset($totals['subtotal'])) {
            if ($config->displayCartSubtotalBoth()) {
                if ($skipTax) {
                    $subtotal = $totals['subtotal']->getValueExclTax();
                } else {
                    $subtotal = $totals['subtotal']->getValueInclTax();
                }
            } elseif($config->displayCartSubtotalInclTax()) {
                $subtotal = $totals['subtotal']->getValueInclTax();
            } else {
                $subtotal = $totals['subtotal']->getValue();
                if (!$skipTax && isset($totals['tax'])) {
                    $subtotal+= $totals['tax']->getValue();
                }
            }
        }
        return $subtotal;
    }
    
    public function getSubtotalInclTax($cart)
    {
        if (!Mage::getSingleton('tax/config')->displayCartSubtotalBoth()) {
            return 0;
        }
        return $this->getSubtotal($cart, false);
    }
    
    //replace js in one place    
    private function replaceJs($result)
    {
         $arrScript = array();
         $result['script'] = '';               
         preg_match_all("@<script type=\"text/javascript\">(.*?)</script>@s",  $result['message'], $arrScript);
         $result['message'] = preg_replace("@<script type=\"text/javascript\">(.*?)</script>@s",  '', $result['message']);
         foreach($arrScript[1] as $script){ 
             $result['script'] .= $script;                 
         }
         $result['script'] =  preg_replace("@var @s",  '', $result['script']); 
         return $result;
    }                              
}
