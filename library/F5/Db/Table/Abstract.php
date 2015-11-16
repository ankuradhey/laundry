<?php

class F5_Db_Table_Abstract extends Zend_Db_Table_Abstract
{
    protected function _setupTableName()
    {
        if(defined('T_PREFIX')) {
            $this->_name = T_PREFIX . $this->_name;
        }
        parent::_setupTableName();        
    }
}
    
?>