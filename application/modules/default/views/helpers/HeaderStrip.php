<?php

class Zend_View_Helper_HeaderStrip extends Zend_View_Helper_Partial
{
    public function getFullStrip($headline, $userInfo){
        return $this->partial('index/headerstrip.phtml',array('headline'=>$headline, 'userInfo'=>$userInfo))
    }
}