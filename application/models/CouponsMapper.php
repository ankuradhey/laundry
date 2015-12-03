<?php

class Application_Model_CouponsMapper
{
    protected $_db_table;
    
    public function __construct()
    {
        $this->_db_table = new Application_Model_DbTable_Coupons();
        
    }
    public function addNewCoupon(Application_Model_Coupons $coupon)
    {
        $data= array(
            
                       
            "coupon_code"=>$coupon->__get("coupon_code"),
            "coupon_value"=>$coupon->__get("coupon_value"),
            "coupon_type"=>$coupon->__get("coupon_type"),
			
			"coupon_last_date"=>date("Y-m-d",strtotime($coupon->__get("coupon_last_date"))),
			"coupon_occourence"=>$coupon->__get("coupon_occourence"),
			"coupon_min_billing"=>$coupon->__get("coupon_min_billing"),
			"coupon_max_discount"=>$coupon->__get("coupon_max_discount"),
			"coupon_status"=>$coupon->__get("coupon_status"),
			
        );
        
        $result=$this->_db_table->insert($data);
        return($result);
    }
    public function getAllCoupons()
    {
        $result = $this->_db_table->fetchAll(NULL , array('coupon_id DESC'));
        if(count($result)==0)
        {
            return false;
        }
        $coupon_arr = array();
        foreach($result as $row)
        { 
            $coupon = new Application_Model_Coupons($row);
            $coupon_arr[] = $coupon;
        }
        return $coupon_arr;
    }
	
    public function getCouponByCouponCode($code) {
        //$where = array("LOWER(coupon_code) =?" => strtolower($code));
        $query = "SELECT * FROM coupons WHERE LOWER(`coupon_code`)='".strtolower($code)."'";
        $stmt = $this->_db_table->getAdapter()->query($query);
        $result = $stmt->fetch();
        //$result = $this->_db_table->fetchRow($where);
        //print_r($result);echo "jfj";
        if (!$result) {
            return false;
        }
        $coupon_object = new Application_Model_Coupons();
        foreach($result as $key=>$value)
        {
            $coupon_object->__set($key,$value);
        }
        return $coupon_object;
    }
	
    public function getCouponById($id)
    {
        $where = array(
            "coupon_id = ?" => $id
        );
        $result = $this->_db_table->fetchRow($where);
        if(!$result)
        {
            return FALSE;
            
        }
        $coupon = new Application_Model_Coupons($result);
        return $coupon;
    }
    public function updateCoupon(Application_Model_Coupons $coupon)
    {
        $data= array(
                       
            "coupon_code"=>$coupon->__get("coupon_code"),
            "coupon_value"=>$coupon->__get("coupon_value"),
            "coupon_type"=>$coupon->__get("coupon_type"),
			
			"coupon_last_date"=>date("Y-m-d",strtotime($coupon->__get("coupon_last_date"))),
			"coupon_occourence"=>$coupon->__get("coupon_occourence"),
			"coupon_min_billing"=>$coupon->__get("coupon_min_billing"),
			"coupon_max_discount"=>$coupon->__get("coupon_max_discount"),
			"coupon_status"=>$coupon->__get("coupon_status"),
			
        );
        $where = array(
            "coupon_id = ?" => $coupon->__get("coupon_id")
        );
		
		try{			

			$updated_records = $this->_db_table->update($data, $where);	
			
			return (object)array("success"=>true,"error"=>false,"message"=>"Record Successfully Updated","row_affected"=>$updated_records) ;			
						
 		}
		catch(Zend_Exception  $e) {/* Handle Exception Here  */
					
			return (object)array("success"=>false,"error"=>true,"message"=>$e->getMessage(),"exception"=>true,"exception_code"=>$e->getCode()) ;
			
 		}
				        		
        return $result;
    }
    public function deleteCouponById($id)
    {
        $where = array(
            "coupon_id = ?" => $id
        );
        $result = $this->_db_table->delete($where);
        return $result;
    }

   
}

