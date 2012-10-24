<?php
class Krirob_Badge_Block_Detail extends Mage_Catalog_Block_Product_Abstract {
    
    protected function _toHtml() {
        $result = Mage::getModel('krbadge/badge')->getBadge($this->getProduct());
        if($result['badge'] == 'none'){
            $html = '';
        }elseif($result['badge'] == 'special_price'){
            $image = Mage::getDesign()->getSkinUrl('/images/badges/splash_new_blue.png');
            $html = '<div style="background-image:url('.$image.');background-repeat:no-repeat;height:50px;width:50px;position:absolute;top:0;left:0;">'.$result['badge'].' '.$result['discount'].'%</div>';
        }else{
            $image = Mage::getDesign()->getSkinUrl('/images/badges/splash_new_blue.png');
            $html = '<div style="background-image:url('.$image.');background-repeat:no-repeat;height:50px;width:50px;position:absolute;top:0;left:0;">'.$result['badge'].'</div>';
        }
        return $html;
    }    
}
?>