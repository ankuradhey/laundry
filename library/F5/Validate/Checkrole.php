<?php

class Validate_Checkrole extends Zend_Validate_Abstract
{
    const ROLE_EXISTS = 'role_exists';
    
    protected $_messageTemplates = array(
        self::ROLE_EXISTS => 'ERROR_ROLENAME_EXISTS'
    );

    public function isValid($value, $context = null)
    {
        $this->_setValue($value);
        
        $db = Zend_Registry::get('db');

        $query = ' SELECT * FROM ' . T_PREFIX . 'user_acl_roles '
               . ' WHERE role_name = ? ';

        if ($db->fetchAll($query, array($value))) {
            $this->_error(self::ROLE_EXISTS);
            return false;
        }

        return true;
    }
}
