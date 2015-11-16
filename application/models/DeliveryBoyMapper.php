<?php

class Application_Model_DeliveryBoyMapper
{
    protected $_db_table;
    public function __construct()
    {
            $this->_db_table = new Application_Model_DbTable_DeliveryBoy();
    }

    public function getAllDeliveryBoys()
    {
        $result = $this->_db_table->fetchAll(null,array('delboy_id DESC'));
        if( count($result) == 0 ) {
                return false;
        }
        $deliveryBoy_object_arr = array();
        foreach ($result as $row)
        {
                $deliveryBoy_object = new Application_Model_DeliveryBoy($row);
                array_push($deliveryBoy_object_arr,$deliveryBoy_object);
        }
        return $deliveryBoy_object_arr;
    }
}