<?php
class Users_Model_Event extends Zend_Db_Table
{
 protected $_name = 'user_events';
 
 public function updateanywhere($mytable, array $data, $where)
     {
		 //echo $where;die;
      $db = Zend_Db_Table::getDefaultAdapter();
      $db->update($mytable,$data,$where);
      
      return true;
     }
 
   public function getevents($user_id)
    {
        $_name  = $this->_name;
        $db     = Zend_Db_Table::getDefaultAdapter();
               $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $sql    = $db->select()->from($_name)->where('user_id = ? ', $user_id);                  
        $result = $db->fetchAll($sql);          
        return $result;
    }   
    
 public function deleteEvents($id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = "DELETE  FROM user_events WHERE events_id='".$id."'";
        $result = $this->_db->query($sql);
        return $result;
    }   
    
 public function geteventsById($id) 
 {
    $_name  = $this->_name;
    $db     = Zend_Db_Table::getDefaultAdapter();
    $sql    = $db->select()->from($_name)->where('events_id = ? ', $id);                  
    $result = $db->fetchRow($sql);          
    return $result; 
 }
 
 public function geteventsName($id)
 {
 $_name  = $this->_name;
    $db     = Zend_Db_Table::getDefaultAdapter();
    $sql    = $db->select(array('events_id','event_title'))->from($_name)->where('events_id = ? ', $id);                  
    $result = $db->fetchRow($sql);          
    return $result;     
     
 }
 
 public function checkByEmail($email,$flag) 
 {
    $db     = Zend_Db_Table::getDefaultAdapter();
    $sql    = "select * from user WHERE email = '$email'";                 
    $result = $db->fetchRow($sql);          
    if(empty($flag))
    {    
    if(!empty($result)){ return true; }else{return false;}
    }else{
        
     return $result;   
    }
    
  }
 
 public function getInvitEvents($usersessid)
 {
     $db     = Zend_Db_Table::getDefaultAdapter();
     $db->setFetchMode(Zend_Db::FETCH_OBJ);
     $sql    = "select eiu.*,ue.*,u.display_name from events_invite_user eiu ,user_events ue,user u WHERE eiu.events_id = ue.events_id AND eiu.user_id = u.user_id AND eiu.invite_user='$usersessid'";                
     $result = $db->fetchAll($sql);          
     return $result;  
     
 }
 
 public function work_type($name,$type)
 {
    
    $field =($type == 'works')? "work_name": (($type == 'collage')? 'collage_name': (($type == 'school')? 'school_name': 'skills'));      
    $db     = Zend_Db_Table::getDefaultAdapter();
    $sql    = "SELECT $field FROM user_about WHERE $field like '%$name%'";                  
    $result = $db->fetchAll($sql);          
    return $result;          
 }
 
 public function getuserSkill($name)
 {
    $db     = Zend_Db_Table::getDefaultAdapter();
     $db->setFetchMode(Zend_Db::FETCH_OBJ);
    $sql    = "SELECT skills as name,id FROM user_about WHERE skills like '%$name%'";                  
    $result = $db->fetchAll($sql);          
    return $result;          
 }
 
 public function checkuserdata($userid)
 {
    $db     = Zend_Db_Table::getDefaultAdapter();
    $db->setFetchMode(Zend_Db::FETCH_OBJ);
    $sql    = "SELECT * FROM user_about WHERE user_id = '$userid'";                  
    $result = $db->fetchRow($sql);    
    return $result;            
 } 
 
 public function getuserAboutInfo($userid)
 {
    $db     = Zend_Db_Table::getDefaultAdapter();
    $db->setFetchMode(Zend_Db::FETCH_OBJ);
    $sql    = "SELECT * FROM user_about WHERE user_id = '$userid'";                  
    $result = $db->fetchRow($sql);    
    return $result;    
 }     
 
public function getuserbasicInfo($userid)
{
    $db     = Zend_Db_Table::getDefaultAdapter();
    $db->setFetchMode(Zend_Db::FETCH_OBJ);   
    $select = $db->select()
    ->from(array('v' => 'user'), array('user_id','email', 'dob', 'sex','month','day','year'))
    ->joinLeft(array('r' => 'user_profile_field'), 'v.user_id = r.userid',array('phone','website','country','state','city','address','zipcode','website','fbpage','twpage','inspage','vine','youtube'))
    ->joinLeft(array('t' => 'user_about'), 'v.user_id = t.user_id',array('id','places','places2'))        
    ->where('v.user_id = ?', $userid);
    
    $result = $db->fetchRow($select);    
    return $result;
}

public function getprofilebasicinfo($userId)
{
    $db     = Zend_Db_Table::getDefaultAdapter();
    $db->setFetchMode(Zend_Db::FETCH_OBJ);   
    $select = $db->select()
    ->from(array('v' => 'user'), array('user_id','sex','month','day','year'))
    ->joinLeft(array('r' => 'user_profile_field'), 'v.user_id = r.userid',array('marrital_status','country','state','city','address'))
    ->joinLeft(array('t' => 'user_about'), 'v.user_id = t.user_id',array('about_status'))        
    ->where('v.user_id = ?', $userId);
    
    $result = $db->fetchRow($select);    
    return $result;
}  

public function getaboutStatus($id)
 {
    $db     = Zend_Db_Table::getDefaultAdapter();
     $db->setFetchMode(Zend_Db::FETCH_OBJ);
    $sql    = "SELECT * FROM user_about WHERE id='$id'";                  
    $result = $db->fetchRow($sql);          
    return $result;          
 }
 
public function checkuserprofiledata($userid)
{
    $db     = Zend_Db_Table::getDefaultAdapter();
    $db->setFetchMode(Zend_Db::FETCH_OBJ);
    $sql    = "SELECT * FROM user_profile_field WHERE userid = '$userid'";                  
    $result = $db->fetchRow($sql);    
    return $result;    
}

public function getallcity() 
{
    $db     = Zend_Db_Table::getDefaultAdapter();
    $db->setFetchMode(Zend_Db::FETCH_OBJ);
    $sql    = "SELECT * FROM cities ";                  
    $result = $db->fetchAll($sql);    
    return $result; 
    
}

 //end function 
}

?>