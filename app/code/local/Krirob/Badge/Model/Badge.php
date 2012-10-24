<?php
class Krirob_Badge_Model_Badge extends Mage_Core_Model_Abstract {

    protected $_product;

    public function getBadge($product){
        $this->_product = $product;
        $badge = array('badge' => 'none');
        if($this->_isNew()){
            $badge = array('badge' => 'new');
        }elseif($this->_isSpecialPrice()){
            $percent = $this->_getDiscountPercent();
            $badge = array('badge' => 'special_price', 'discount' => $percent);
        }elseif($this->_isBestseller()){
            $badge = array('badge' => 'bestseller');
        }elseif($this->_isPopular()){
            $badge = array('badge' => 'popular');
        }
        return $badge;
    }
    
    protected function _isNew(){
        if(!Mage::getStoreConfig('badge/new/enabled')){
            return false;
        }
        
        $mode = Mage::getStoreConfig('badge/new/mode');
        switch($mode){
            case 1:
                return $this->_fixedNewMode();
                break;
            case 2:
                return $this->_dynamicNewMode();
                break;
        }

    }
    
    protected function _fixedNewMode(){
        $days = Mage::getStoreConfig('badge/new/days');
        $date = Varien_Date::formatDate(time(),true);
        $current_date = new DateTime($date);
        
        $creation_date = $this->_product->getData('created_at');
        $new_date = new DateTime($creation_date);
        $new_date->add(new DateInterval('P'.$days.'D'));
        if($current_date <= $new_date){
            return true;
        }else{
            return false;
        }
    }
    
    protected function _dynamicNewMode(){
        $date = Varien_Date::formatDate(time(),true);
        
        if(!$this->_product->getData('news_from_date')) {
            return false;
        }
        
        $current_date = new DateTime($date); // compare date
        $from_date = new DateTime($this->_product->getData('news_from_date')); // begin date
        $to_date = new DateTime($this->_product->getData('news_to_date')); // end date
        
        $return = ($current_date >= $from_date && $current_date <= $to_date);
        return $return;
    }
    
    protected function _isSpecialPrice(){
        if(!Mage::getStoreConfig('badge/special_price/enabled')){
            return false;
        }
        if(!$this->_product->getData('special_from_date')) {
            return false;
        }
        $date = Varien_Date::formatDate(time(),true);
        $current_date = new DateTime($date); // compare date
        $from_date = new DateTime($this->_product->getData('special_from_date')); // begin date
        $to_date = new DateTime($this->_product->getData('special_to_date')); // end date
        
        $return = ($current_date >= $from_date && $current_date <= $to_date);
        return $return;
    }
    
    protected function _isPopular(){
        if(!Mage::getStoreConfig('badge/popular/enabled')){
            return false;
        }
        $storeId    = Mage::app()->getStore()->getId();
        $number = Mage::getStoreConfig('badge/popular/days');
        $products = Mage::getResourceModel('reports/product_collection')
            ->addViewsCount()
            ->setStoreId($storeId)
            ->addAttributeToSort('views', 'desc')
            ->setPageSize($number)
            ->load();
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);

        foreach($products->getItems() as $p){
            if($p->getId()  == $this->_product->getId()){
                return true;
            }
        }
        return false;
    }
    
    protected function _isBestseller(){
        if(!Mage::getStoreConfig('badge/bestseller/enabled')){
            return false;
        }
        $storeId    = Mage::app()->getStore()->getId();
        $number = Mage::getStoreConfig('badge/bestseller/days');
        $products = Mage::getResourceModel('reports/product_collection')
            ->addOrderedQty()
            ->setStoreId($storeId)
            ->addAttributeToSort('views', 'desc')
            ->setPageSize($number)
            ->load();
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);

        foreach($products->getItems() as $p){
            if($p->getId()  == $this->_product->getId()){
                return true;
            }
        }
        return false;
    }
    
    protected function _getDiscountPercent(){
        $specialPrice = $this->_product->getFinalPrice();
        $price = $this->_product->getPrice();
        $foo = $specialPrice / $price;
        $bar = (1.00 - $foo)*100;
        return round($bar);
    }
    
}