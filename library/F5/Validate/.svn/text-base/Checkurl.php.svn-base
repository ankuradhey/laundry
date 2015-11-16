<?php

class Validate_Checkurl extends Zend_Validate_Abstract
{
    const INVALID_URL = 'invalid_url';
    
    protected $_messageTemplates = array(
        self::INVALID_URL => "'%value%' is not a valid URL"
    );

    public function isValid($value)
    {
        $this->_setValue($value);
        
        if (!Zend_Uri::check($value)) {
            $this->_error(self::INVALID_URL);
            return false;
        }

        return true;
    }
}