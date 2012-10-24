<?php

class Krirob_Badge_Model_System_Config_Source_Newmode {
    
    public function toOptionArray() {

        return array(array('label'=>'Fixed','value' => '1'),
                     array('label'=>'Dynamic','value' => '2'));
    }
    
}