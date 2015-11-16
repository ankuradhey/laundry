<?php 
class Application_Model_ServiceMasterMapper
{
    protected $_db_table;
    public function __construct()
    {
            $this->_db_table = new Application_Model_DbTable_ServiceMasters();
    }

    public function addNewServiceMaster(Application_Model_ServiceMasters $serviceMaster)
    {
        $data = array(
	'service_name' => $serviceMaster->__get("service_name"),
	);
        $result = $this->_db_table->insert($data);
        if(count($result)==0)
        {
                return false;
        }
        else
        {
            return $result;
        }
    }
    public function getServiceMasterById($service_id)
    {
        $where = array(
            "service_id =?" => $service_id
        );
        $result = $this->_db_table->fetchRow($where);
        if( count($result) == 0 ) {
                return false;
        }
        $serviceMaster = new Application_Model_ServiceMasters($result);
        return $serviceMaster;
    }
    public function getAllServiceMasters()
    {
        $result = $this->_db_table->fetchAll(null,array('service_id ASC'));
        if( count($result) == 0 ) {
                return false;
        }
        $serviceMaster_object_arr = array();
        foreach ($result as $row)
        {
                $serviceMaster_object = new Application_Model_ServiceMasters($row);
                array_push($serviceMaster_object_arr,$serviceMaster_object);
        }
        return $serviceMaster_object_arr;
    }
    public function updateServiceMaster(Application_Model_ServiceMasters $serviceMaster)
    {
        $data = array(
	'service_name' => $serviceMaster->__get("service_name"),
	);
        $where = "service_id = " . $serviceMaster->__get("service_id");
        $result = $this->_db_table->update($data,$where);
        if(count($result)==0)
        {
                return false;
        }
        else
        {
            return true;
        }
    }
    public function deleteServiceMasterById($service_id)
    {
        $where = "service_id = " . $service_id;
        $result = $this->_db_table->delete($where);
        if(count($result)==0)
        {
                return false;
        }
        else
        {
            return true;
        }
    }
}
