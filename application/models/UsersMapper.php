<?php 

class Application_Model_UsersMapper
{
    protected $_db_table;
    
    public function __construct()
    {
        $this->_db_table = new Application_Model_DbTable_Users();
        
    }
    public function addNewUser(Application_Model_Users $user)
    {
        $data= array(
            
                       
            "user_fname"=>$user->__get("user_fname"),
            "user_lname"=>$user->__get("user_lname"),
            "user_email"=>$user->__get("user_email"),
            "hashed_password"=>$user->__get("hashed_password"),
            "user_fb_id"=>$user->__get("user_fb_id"),
            "user_address"=>$user->__get("user_address"),
            "user_address_additional"=>$user->__get("user_address_additional"),
            "user_number"=>$user->__get("user_number"),
            "user_locality"=>$user->__get("user_locality"),
            "user_landmark"=>$user->__get("user_landmark"),
            "user_city"=>$user->__get("user_city"),
            "user_state"=>$user->__get("user_state"),
            "user_country"=>$user->__get("user_country"),
            "referred_by"=>$user->__get("referred_by"),
        );
        
        $result=$this->_db_table->insert($data);
        return($result);
    }
    public function getAllUsers()
    {
        $result = $this->_db_table->fetchAll(NULL , array('user_id DESC'));
        if(count($result)==0)
        {
            return false;
        }
        $users_arr = array();
        foreach($result as $row)
        { 
            $user = new Application_Model_Users($row);
            $users_arr[] = $user;
        }
        return $users_arr;
    }
    public function getUserById($id)
    {
        $where = array(
            "user_id = ?" => $id
        );
        $result = $this->_db_table->fetchRow($where);
        if(!$result)
        {
            $user = new Application_Model_Users();
            $user->user_fname = "--DELETED--";
            return $user;
        }
        $user = new Application_Model_Users($result);
        return $user;
    }
    public function updateUser(Application_Model_Users $user)
    {
        $data= array(
            
           "user_fname"=>$user->__get("user_fname"),
            "user_lname"=>$user->__get("user_lname"),
            "user_email"=>$user->__get("user_email"),
            "hashed_password"=>$user->__get("hashed_password"),
            "user_fb_id"=>$user->__get("user_fb_id"),
            "user_address"=>$user->__get("user_address"),
			"user_address_additional"=>$user->__get("user_address_additional"),						
            "user_number"=>$user->__get("user_number"),
            "user_locality"=>$user->__get("user_locality"),
            "user_landmark"=>$user->__get("user_landmark"),
            "user_city"=>$user->__get("user_city"),
            "user_state"=>$user->__get("user_state"),
            "user_country"=>$user->__get("user_country"),
            "reset_code"=>$user->__get("reset_code"),
            "referred_by"=>$user->__get("referred_by"),
        );
        $where = array(
            "user_id = ?" => $user->__get("user_id")
        );
		
		try{			
		
        	$updated_records = $this->_db_table->update($data, $where);
		
			return (object)array("success"=>true,"error"=>false,"message"=>"Record Successfully Updated","row_affected"=>$updated_records) ;
		
		}
		catch(Zend_Exception  $e) {/* Handle Exception Here  */
					
			return (object)array("success"=>false,"error"=>true,"message"=>$e->getMessage(),"exception"=>true,"exception_code"=>$e->getCode()) ;
			
 		}
        
    }
    public function deleteUserById($id)
    {
        $where = array(
            "user_id = ?" => $id
        );
        $result = $this->_db_table->delete($where);
        return $result;
    }

    
     public function getUserByEmail($email)
    {
        $where = array(
            "user_email = ?" => $email
        );
        $result = $this->_db_table->fetchRow($where);
        if(!$result)
        {
            $user = new Application_Model_Users();
            $user->user_fname = "--DELETED--";
            return false;
        }
        $user = new Application_Model_Users($result);
        return $user;
    }
    public function getUserByResetCode($code)
    {
        $where = array(
            "reset_code = ?" => $code
        );
        $result = $this->_db_table->fetchRow($where);
        if(!$result)
        {
            return false;
        }
        $user = new Application_Model_Users($result);
        return $user;
    }
    public function getUserByFbId($fbid)
    {
        $where = array(
            "user_fb_id = ?" => $fbid
        );
        $result = $this->_db_table->fetchRow($where);
        if(!$result)
        {
            $user = new Application_Model_Users();
            $user->user_name = "--DELETED--";
            return $user;
        }
        $user = new Application_Model_Users($result);
        return $user;
    }
    
    
}

