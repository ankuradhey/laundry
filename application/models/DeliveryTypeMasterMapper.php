<?php
class Application_Model_DeliveryTypeMasterMapper
{
    protected $_db_table;
    public function __construct()
    {
            $this->_db_table = new Application_Model_DbTable_DeliveryTypeMaster();
    }

    public function addNewDeliveryTypeMaster(Application_Model_DeliveryTypeMaster $deliveryType)
    {
        $data = array(
	'delivery_type_name' => $deliveryType->__get("delivery_type_name"),
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
    public function getDeliveryTypeMasterById($delivery_type_id)
    {
        $where = array(
            "delivery_type_id =? " => $delivery_type_id
        );
        $result = $this->_db_table->fetchRow($where);
        if( count($result) == 0 ) {
                return false;
        }
        
        $deliveryType = new Application_Model_DeliveryTypeMaster($result);
        return $deliveryType;
    }
    public function getAllDeliveryTypeMaster()
    {
        $result = $this->_db_table->fetchAll(null,array('delivery_type_id ASC'));
        if( count($result) == 0 ) {
                return false;
        }
        $deliveryType_object_arr = array();
        foreach ($result as $row)
        {
                $deliveryType_object = new Application_Model_DeliveryTypeMaster($row);
                array_push($deliveryType_object_arr,$deliveryType_object);
        }
        return $deliveryType_object_arr;
    }
    public function updateDeliveryTypeMaster(Application_Model_DeliveryTypeMaster $deliveryType)
    {
        $data = array(
	'delivery_type_name' => $deliveryType->__get("delivery_type_name"),
	);
        $where = "delivery_type_id = " . $deliveryType->__get("delivery_type_id");
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
    public function deleteDeliveryTypeMasterById($delivery_type_id)
    {
        $where = "delivery_type_id = " . $delivery_type_id;
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
