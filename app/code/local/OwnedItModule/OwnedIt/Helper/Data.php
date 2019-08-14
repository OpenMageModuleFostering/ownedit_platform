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
class OwnedItModule_OwnedIt_Helper_Data extends Mage_Catalog_Helper_Data {

    private $configuration = null;
    public function getGenConf() {
        if (!$this->configuration) {
            $this->configuration = new Varien_Object;
            $this->configuration->setData(Mage::getStoreConfig('ownedit/general'));
        }
        return $this->configuration;
    }
    
    public function getStoreId() {
        return $this->getGenConf()->getStoreId();
    }

    public function isActive() {
        return ($this->getGenConf()->getEnabled() && $this->getStoreId());
    }

    public function getAnchorClass() {
        return $this->getGenConf()->getAnchorCssClass();
    }

}
