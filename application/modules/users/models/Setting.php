<?php
class Users_Model_Setting extends Zend_Db_Table
{
 protected $_name = 'manage_privacy';
 
 public function updateanywhere($mytable, array $data, $where)
     {
		 //echo $where;die;
      $db = Zend_Db_Table::getDefaultAdapter();
      $db->update($mytable,$data,$where);
      
      return true;
     }
	public function insertanywhere($mytable, array $data)
    {
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->insert($mytable,$data);
		$user_id = $db->lastInsertId();
		return  $user_id ;
    } 
  public function fetchRegisOne($uid)
	{ 
	  
		$db          = Zend_Db_Table::getDefaultAdapter();
		$db->setFetchMode(Zend_Db::FETCH_OBJ);	 
		$query_city  = "SELECT * FROM user WHERE user_id='$uid'";
		$row         = $db->fetchAll($query_city);
		
		return $row;		
	}
 public function fetchRegisName($uid)
 {
	    $db             = Zend_Db_Table::getDefaultAdapter();
		$chk_invite     = "SELECT display_name FROM user WHERE user_id='".$uid."'";
		$result         = $db->fetchAll($chk_invite);
		return $result; 
 }	
 public function PrivacyShow($uid)
	{  
	 
	    $db = Zend_Db_Table::getDefaultAdapter();	
		$db->setFetchMode(Zend_Db::FETCH_OBJ);	 	
        $privacy_user  = "SELECT * FROM  manage_privacy WHERE userid='".$uid."'";
		$privacyResult = $db->fetchAll($privacy_user);
		return  $privacyResult;				
	}
 public function chkoldpaswd($oldpass,$userid)
  {
	    $db = Zend_Db_Table::getDefaultAdapter();	
		$db->setFetchMode(Zend_Db::FETCH_OBJ);	 	
        $privacy_user  = "SELECT * FROM  user WHERE password='".$oldpass."' AND user_id='".$userid."'";
		$privacyResult = $db->fetchAll($privacy_user);
		return  $privacyResult;	
  }
   public function chkuseremail($email)
  {
	    $db = Zend_Db_Table::getDefaultAdapter();	
		$db->setFetchMode(Zend_Db::FETCH_OBJ);	 	
        $privacy_user  = "SELECT * FROM  user WHERE email = '".$email."'";
		$privacyResult = $db->fetchAll($privacy_user);
		return  $privacyResult;	
  }
    public function chkperemail($email)
  {
	    $db = Zend_Db_Table::getDefaultAdapter();	
		$db->setFetchMode(Zend_Db::FETCH_OBJ);	 	
        $privacy_user  = "SELECT * FROM  email_preference WHERE email = '".$email."'";
		$privacyResult = $db->fetchAll($privacy_user);
		return  $privacyResult;	
  }
 public function fetch_last_change($uid)
  {
	    $db = Zend_Db_Table::getDefaultAdapter();	
		$db->setFetchMode(Zend_Db::FETCH_OBJ);	 	
        $privacy_user  = "SELECT registration_date,last_activity_date FROM user WHERE user_id='".$uid."'";
		$privacyResult = $db->fetchAll($privacy_user);
		return  $privacyResult;	
  }
  public function fetch_email_preference($uid)
  {
	    $db = Zend_Db_Table::getDefaultAdapter();	
		$db->setFetchMode(Zend_Db::FETCH_OBJ);	 	
        $privacy_user  = "SELECT * FROM email_preference WHERE userid='".$uid."'";
		$privacyResult = $db->fetchAll($privacy_user);
		return  $privacyResult;	
  }
  
  public function delete_users_email($id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $sql = "DELETE  FROM email_preference WHERE  id='".$id."'";
        $result = $this->_db->query($sql);
        return $result;
    }
}

?>