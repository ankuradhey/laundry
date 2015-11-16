<?php

class Validate_Checkuser extends Zend_Validate_Abstract
{
    const USER_EXISTS = 'user_exists';
    
    protected $_messageTemplates = array(
        self::USER_EXISTS => 'ERROR_USERNAME_TAKEN'
    );

    public function isValid($value)
    {
        $this->_setValue($value);
        
        $db = Zend_Registry::get('db');

        $query = ' SELECT * FROM ' . T_PREFIX . 'user_users '
               . ' WHERE user_username = ? ';

        if ($db->fetchAll($query,array($value))) {
            $this->_error(self::USER_EXISTS);
            return false;
        }

        return true;
    }
}
