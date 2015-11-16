<?php

class Users_Model_User extends Zend_Db_Table
{
    protected $_name = 'user';

  public function insertanywhere($mytable, array $data)
    {
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->insert($mytable,$data);
		$user_id = $db->lastInsertId();
		return  $user_id ;
    }
   public function updateanywhere($mytable, array $data, $where)
     {
		 //echo $where;die;
      $db = Zend_Db_Table::getDefaultAdapter();
      $db->update($mytable,$data,$where);
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
           
           public function checkuserByProviderId($provider_id)
           {
            $_name  = 'users' ;
            $db     = Zend_Db_Table::getDefaultAdapter();
			$db->setFetchMode(Zend_Db::FETCH_OBJ);
			
            $sql    = $db->select()->from($_name)
                    //->where('status = ? ', 1)
                    ->where('user_fb_id = ? ', $provider_id);
					
            $result = $db->fetchAll($sql);          
            return $result;   
           }
           public function getallproduct()
           {

                $db     = Zend_Db_Table::getDefaultAdapter();
                $sql    = "SELECT * FROM items it,item_price ip where it.item_id=ip.item_id And it.is_live='1' And it.item_image !='' order by item_order";                  
                $result = $db->fetchAll($sql);          
                return $result; 
           }
           
           public function getallproductcategory()
          {
           $db     = Zend_Db_Table::getDefaultAdapter();
                $sql    = "SELECT * FROM `categories` WHERE is_live='1' order by category_order";                  
                $result = $db->fetchAll($sql);          
                return $result;     
           }

                      public function calculateAge($date)
	{
		list($year,$month,$day) = explode("-",$date);
		$year_diff  = date("Y") - $year;
		$month_diff = date("m") - $month;
		$day_diff   = date("d") - $day;
		if ($day_diff < 0 || $month_diff < 0) $year_diff--;
		return $year_diff;
}

  public function calculateday($date)
	{
		list($year,$month,$day) = explode("-",$date);		
		$day_diff               = date("d") - $day;
		//if ($day_diff < 0 || $month_diff < 0) $year_diff--;
		return $day_diff;
   }
} //Close Class
?>