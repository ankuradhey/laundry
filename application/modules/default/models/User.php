<?php

class Default_Model_User extends Zend_Db_Table
{
    protected $_name = 'user';

  public function insertanywhere($mytable, array $data)
    {
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->insert($mytable,$data);
		$user_id = $db->lastInsertId();
		return  $user_id ;
    }
    
    public function getuserById($id)
	{
            $_name  = 'users' ;
            $db     = Zend_Db_Table::getDefaultAdapter();
			$db->setFetchMode(Zend_Db::FETCH_OBJ);
			
            $sql    = $db->select()->from($_name)
                    //->where('status = ? ', 1)
                    ->where('user_id = ? ', $id);
					
            $result = $db->fetchAll($sql);          
            return $result;
	   }

}




?>