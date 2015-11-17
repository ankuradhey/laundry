<?php 

class Application_Model_PackagesMapper {

    protected $_db_table;

    public function __construct() {
        $this->_db_table = new Application_Model_DbTable_Packages();
    }

    public function addNewPackage(Application_Model_Packages $package) {
        $data = array(
            "package_name" => $package->__get("package_name"),
            "package_price" => $package->__get("package_price"),
            "package_icon" => $package->__get("package_icon"),
            "no_of_clothes" => $package->__get("no_of_clothes"),
            "saving_percent" => $package->__get("saving_percent"),
            "validity" => $package->__get("validity"),
            "no_of_pickups" => $package->__get("no_of_pickups"),
            "package_service_type" => $package->__get("package_service_type")
        );

        $result = $this->_db_table->insert($data);
        return($result);
    }

    public function getAllPackages() {
        
        $result = $this->_db_table->fetchAll(NULL, array('package_id ASC'));
        if (count($result) == 0) {
            return false;
        }
        $packages_arr = array();
        foreach ($result as $row) {
            $package = new Application_Model_Packages($row);
            $packages_arr[] = $package;
        }
        return $packages_arr;
    }

    public function getPackageById($id){
        $where = array(
            "package_id = ?" => $id
        );
        $result = $this->_db_table->fetchRow($where);
        if (!$result) {

            return FALSE;
        }
        $package = new Application_Model_Packages($result);
        return $package;
    }


    public function updatePackage(Application_Model_Packages $package) {
        $data = array(
            "package_name" => $package->__get("package_name"),
            "package_price" => $package->__get("package_price"),
            "package_icon" => $package->__get("package_icon"),
            "no_of_clothes" => $package->__get("no_of_clothes"),
            "saving_percent" => $package->__get("saving_percent"),
            "validity" => $package->__get("validity"),
            "no_of_pickups" => $package->__get("no_of_pickups")
        );
        $where = array(
            "package_id = ?" => $package->__get("package_id")
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
