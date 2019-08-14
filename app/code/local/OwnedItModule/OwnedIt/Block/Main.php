<?php

/**
 *Product Name : Owned it Addon / Plugin
 *Copyright (c) 2015 Owned it Ltd
 *
 * Owned it:
 *
 * NOTICE OF LICENSE
 *
 *Copyright (c) <2015> <Owned it Ltd>
 *
 *Permission is hereby granted, free of charge, to any person 
 *obtaining a copy of this software and associated 
 *documentation files (the "Software"),to deal in the Software 
 *without restriction,including without limitation the rights
 *to use, copy, modify, merge, publish, distribute, sublicense,
 *and/or sell copies of the Software, and to permit persons 
 *to whom the Software is furnished to do so, subject to the 
 *following conditions:
 *
 *The above copyright notice and this permission notice shall be
 *included in all copies or substantial portions of the Software.
 *  
 *THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY 
 *OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT 
 *LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
 *FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 *IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS 
 *BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,             
 *WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
 *ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR  
 *THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 *
 * @license http://www.opensource.org/licenses/mit-license.php(MIT)
 * @author Owned it Ltd. (http://www.ownedit.com)
 *
**/


class OwnedItModule_OwnedIt_Block_Main extends Mage_Core_Block_Template {
    /**
     * Prepate layout
     * @return object
     */
   protected function _getStoreId(){
   	 	return Mage::helper('ownedit')->getStoreId();
   	}
   	
   	protected function _getStatus(){	
   		return Mage::helper('ownedit')->isActive();
   	}
   	
   	protected function _getCurrentLanguage(){
   		return Mage::app()->getLocale()->getLocaleCode();
   	}
	protected function _getOwnedItJS(){
		if($this->_getStatus()) {
			$orderIds = $this->getOrderIds();
        	if (empty($orderIds) || !is_array($orderIds)) {
        		return '<div class="ownedit">'.$this->_getPrepurchaseCode().'</div>';
        	}
        	else{
        		return $this->_getPostpurchaseCode(); 
        	}
        }
	}
	protected function _getCategories($product_id){
		$catArr = array();
		$catIdArr = array();
		$product = Mage::getModel('catalog/product')->load($product_id);
		$cats = $product->getCategoryIds();
		foreach ($cats as $category_id) {
			$_cat = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId())->load($category_id);
			array_push($catIdArr, $category_id);
			array_push($catArr, trim($_cat->getName()));            
		}
		$returnArr['categoryNames'] = $catArr;
		$returnArr['categoryIds'] = $catIdArr;
		return $returnArr;
	}
	protected function _getPrepurchaseCode() {
		$store_id = $this->_getStoreId();
		$language = $this->_getCurrentLanguage();
		$quote = Mage::getModel('checkout/session')->getQuote();
		$quoteData= $quote->getData();
		$grandTotal=sprintf("%.2f",$quoteData['grand_total']);
		$totalProducts = $quoteData['items_count'];
		$cartItems = $quote->getAllVisibleItems();
		$categories = '';$category_ids = array();
		if(Mage::registry('current_product')) {
			$product_id = Mage::registry('current_product')->getId();
			$current_category = '';
			$result = $this->_getCategories($product_id);
			$cat = $result['categoryNames'];
			foreach($cat as $cat_name){
				$current_category .= "productpage_category : '". $cat_name . "',";
			}
			
		}
		$i = 1;	$prodCatArr = array();$prodCatNameArr = array();
        foreach ($cartItems as $item)
        {
            $productId = $item->getProductId();
			$result = $this->_getCategories($productId);
			$catNames = $result['categoryNames'];
			$catIds = $result['categoryIds'];
			for($j=0; $j<count($catNames); $j++){ 
				array_push($prodCatArr, $catIds[$j]);
				array_push($prodCatNameArr, $catNames[$j]);         
			}
			$i++;
         }
		$category_ids["product_category_ids"] = json_encode($prodCatArr); 
		$formKey = Mage::getSingleton('core/session')->getFormKey();
		$owneditJS = "";
		$owneditJS .= "<script type='text/javascript'>";
		$owneditJS .= "var _ownedit = _ownedit || {};";
		$owneditJS .= "_ownedit['custom_variables'] = {";
		$owneditJS .= "language : '".$language."', order_toal : '".$grandTotal."',";
		$owneditJS .= "no_of_products_in_cart : '".$totalProducts."',";
		$owneditJS .= "product_category_ids : '".json_encode($prodCatArr)."',";
		$owneditJS .= "product_category_names : '".json_encode($prodCatNameArr)."',";
		$owneditJS .= "form_key : '".$formKey."'";
		$owneditJS .= $current_category;
		$owneditJS .= "};";
		$owneditJS .= "var ss = document.createElement('script');";
		$owneditJS .= "ss.src = 'https://cdn.ownedit.com/ownedit_js/ownedit.js?store_id=$store_id&prepurchase=true';";
		$owneditJS .= "ss.type = 'text/javascript';";
		$owneditJS .= "ss.async = 'true';";
		$owneditJS .= "ss.id = 'ownedit-js';";
		$owneditJS .= "var s = document.getElementsByTagName('head')[0];s.appendChild(ss);";
		$owneditJS .= "</script>";
		return $owneditJS;
	}
	 
	protected function _getPostpurchaseCode() {
		$owneditJS = "";
		
        $order = Mage::getModel('sales/order')->load(Mage::getSingleton('checkout/session')->getLastOrderId());
	    $email = $order->getCustomerEmail();
	
	    $arr = array();
	    $arr['order_id'] = $order->getRealOrderId();
	    $arr['customer_email'] = $email;
		$arr['order_value'] = $order->getGrandTotal();
		$arr['order_currency'] = $order->getOrderCurrencyCode();
        $arr['store_name']=$store_name;
				
        $store_id = $this->_getStoreId();
        
	    $owneditJS.="<script type=\"text/javascript\" id=\"ownedit-js\" src=\"https://cdn.ownedit.com/ownedit_js/ownedit.js?store_id=$store_id&postpurchase=true\"></script>";
                   
        $itemQty = count($order->getAllVisibleItems());
	    $products = array();			
	
        foreach ($order->getAllVisibleItems() as $item) {
				$product = Mage::getModel('catalog/product')->load($item->getProductId());

                $product_id = $item->getProductId();
                $product_url= $product->getProductUrl();
				$product_image_url = $product->getImageUrl();
                $product_name=$product->getName();
                $store = Mage::app()->getStore();
                $currency=$store->getCurrentCurrencyCode();
                $store_name = $store->getName();
                $price= sprintf("%.2f",$product->getPrice());
	            $category = $product->getCategory();
				$sku = $product->getSku();
                $qty = $item->getQtyToInvoice();
				$cat_name = "";
				if(isset($category))
				{	
					$cat_name = $category->getName();
				}	
				$desc = $product->getShortDescription(); 
				$prod = array();
				$prod['product_name']=$product_name;
				$prod['product_url']=$product_url;
				$prod['product_desc']=$desc;
				$prod['product_image_url']=$product_image_url;
				$prod['product_price']=$price;
				$prod['currency']=$currency;
				$prod['product_id']=$product_id;
				$prod['product_category']=$cat_name;
				$prod['product_sku']=$cat_name;
        		$prod['product_quantiry']=$qty;
				array_push($products,$prod);		
        }
                 
	    $arr['products']=$products;
	    $json = json_encode($arr);	

	    $owneditJS.="<script type=\"text/javascript\">";
            $owneditJS.="function post_to_owned_it(){";
            $owneditJS.="var details =$json;"; 
	    $owneditJS.="post_it(details);}onLoadCallBack(post_to_owned_it);</script>";
	
        return $owneditJS;
	}
}
