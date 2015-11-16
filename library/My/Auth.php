<?php 
class My_Auth extends Zend_Auth
{
    public function __construct($value) {
        $this->setStorage(new Zend_Auth_Storage_Session($value));
    }
    
}
?>
