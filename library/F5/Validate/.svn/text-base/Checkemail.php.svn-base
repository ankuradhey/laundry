<?php

class Validate_Checkemail extends Zend_Validate_Abstract
{
    const EMAIL_EXISTS = 'email_exists';
    
    protected $_messageTemplates = array(
        self::EMAIL_EXISTS => 'Acest email este deja asociat cu un alt utilizator'
    );

    public function isValid($value)
    {
        $this->_setValue($value);
        
        $db = Zend_Registry::get('db');

        $query = ' SELECT * FROM ' . T_PREFIX . 'user_users '
               . ' WHERE user_email=? ';

        if ($db->fetchAll($query,array($value))) {
            $this->_error(self::EMAIL_EXISTS);
            return false;
        }

        return true;
    }
}
