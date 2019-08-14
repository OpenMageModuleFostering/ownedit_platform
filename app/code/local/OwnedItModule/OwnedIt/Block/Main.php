<?php

/**
 *Product Name : Owned it Checkout Addon / Plugin
 *Copyright (c) 2012 Owned it Ltd
 *
 * Owned it:
 *
 * NOTICE OF LICENSE
 *
 *Copyright (c) <2011> <Owned it Ltd>
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
    const ANCHOR_CLASS= 'buttons-set';

    protected function _toHtml() {

        $braggJS = '';

        if ($this->getAnchorClass() && $this->getAnchorClass() != self::ANCHOR_CLASS) {
            $anchorClass = $this->getAnchorClass();
            $braggJS = parent::_toHtml();
        } else {
            $anchorClass = self::ANCHOR_CLASS;
        }

        if (Mage::helper('ownedit')->isActive()) {

        $order = Mage::getModel('sales/order')->load(Mage::getSingleton('checkout/session')->getLastOrderId());
	    $email = $order->getCustomerEmail();
	
	    $arr = array();
	    $arr['order_id'] = $order->getRealOrderId();
	    $arr['customer_email'] = $email;
		$arr['order_value'] = $order->getGrandTotal();
		$arr['order_currency'] = $order->getOrderCurrencyCode();
        $arr['store_name']=$store_name;
				
        $store_id = Mage::helper('ownedit')->getStoreId();
	    $braggJS.="<script type=\"text/javascript\" src=\"https://www.ownedit.com/ownedit_js/ownedit.js?store_id=$store_id&anchor=$anchorClass\"></script>";
                   
        $itemQty = count($order->getAllVisibleItems());
	    $products = array();			
	
        foreach ($order->getAllVisibleItems() as $item) {
				$product = Mage::getModel('catalog/product')->load($item->getProductId());

                $product_id = $item->getProductId();
                $product_url= $product->getProductUrl();
                //$product_image_url=$this->helper('catalog/image')->init($product, 'thumbnail');
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

	    $braggJS.="<script type=\"text/javascript\">";
            $braggJS.="function post_to_owned_it(){";
            $braggJS.="var details =$json;"; 
	    $braggJS.="post_it(details);}onLoadCallBack(post_to_owned_it);</script>";
	
        }
        return $braggJS;
    }

    protected function getAnchorClass() {
        return Mage::helper('ownedit')->getAnchorClass();
    }

}
