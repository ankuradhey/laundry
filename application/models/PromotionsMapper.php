<?php

class Application_Model_PromotionsMapper {

    protected $_db_table;

    public function __construct() {
        $this->_db_table = new Application_Model_DbTable_Promotions();
    }

    public function addNewPromotion(Application_Model_Promotions $offer) {
        $data = array(
						
			"offer_name" => $offer->__get("offer_name"),
			
            "offer_clothes" => $offer->__get("offer_clothes"),
			"offer_service_type" => $offer->__get("offer_service_type"),
						
            "no_of_clothes" => $offer->__get("no_of_clothes"),            
            "no_of_pickups" => $offer->__get("no_of_pickups"),
			
			"offer_amount" => $offer->__get("offer_amount"),
			"offer_status" => $offer->__get("offer_status"),
			
            
        );

        try{			
								
			$insertedId = $this->_db_table->insert($data); 

 			return (object)array("success"=>true,"error"=>false,"message"=>"Record Successfully Inserted","inserted_id"=>$insertedId) ;						
						
 		}
		catch(Zend_Exception  $e) {/* Handle Exception Here  */
					
			return (object)array("success"=>false,"error"=>true,"message"=>$e->getMessage(),"exception"=>true,"exception_code"=>$e->getCode()) ;
			
 		}
    }

    public function getAllOffers() {

        $result = $this->_db_table->fetchAll(NULL, array('offer_id ASC'));
        if (count($result) == 0) {
            return false;
        }
        $offers_arr = array();
        foreach ($result as $row) {
            $offer = new Application_Model_Promotions($row);
            $offers_arr[] = $offer;
        }
        return $offers_arr;
    }

    public function getPromotionById($id) {
        $where = array(
            "offer_id = ?" => $id
        );
        $result = $this->_db_table->fetchRow($where);
        if (!$result) {

            return FALSE;
        }
        $offer = new Application_Model_Promotions($result);
        return $offer;
    }

    public function updatePromotion(Application_Model_Promotions $offer) {
        $data = array(
            "offer_name" => $offer->__get("offer_name"),
            "offer_clothes" => $offer->__get("offer_clothes"),
			"offer_service_type" => $offer->__get("offer_service_type"),
						
            "no_of_clothes" => $offer->__get("no_of_clothes"),            
            "no_of_pickups" => $offer->__get("no_of_pickups"),
			
			"offer_amount" => $offer->__get("offer_amount"),
			"offer_status" => $offer->__get("offer_status"),
			
			
        );
        $where = array(
            "offer_id = ?" => $offer->__get("offer_id")
        );
        try{			
			
			$updated_records = $this->_db_table->update($data, $where);	
			
			return (object)array("success"=>true,"error"=>false,"message"=>"Record Successfully Updated","row_affected"=>$updated_records) ;			
						
 		}
		catch(Zend_Exception  $e) {/* Handle Exception Here  */
					
			return (object)array("success"=>false,"error"=>true,"message"=>$e->getMessage(),"exception"=>true,"exception_code"=>$e->getCode()) ;
			
 		}
    }

    public function deletePackageById($id) {
        $where = array(
            "package_id = ?" => $id
        );
        $result = $this->_db_table->delete($where);
        return $result;
    }

}
