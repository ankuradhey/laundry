<?php
class Application_Model_OrderItemsMapper
{
    protected $_db_table;
    public function __construct()
    {
            $this->_db_table = new Application_Model_DbTable_OrderItems();
    }

    public function addNewOrderItem(Application_Model_OrderItems $order_item)
    {
        $data = array(
	'order_id' => $order_item->__get("order_id"),
	'order_item_id' => $order_item->__get("order_item_id"),
	'order_product_name' => $order_item->__get("order_product_name"),
	'order_type' => $order_item->__get("order_type"),
	'package_id' => $order_item->__get("package_id"),
	'order_service_name' => $order_item->__get("order_service_name"),
	'order_category_name' => $order_item->__get("order_category_name"),
	'unit_price' => $order_item->__get("unit_price"),
	'total_price' => $order_item->__get("total_price"),
	'quantity' => $order_item->__get("quantity"),
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
    public function getOrderItemById($order_item_id)
    {
        $result = $this->_db_table->find($order_item_id);
        if( count($result) == 0 ) {
                return false;
        }
        $row = $result->current();
        $order_item = new Application_Model_OrderItems($row);
        return $order_item;
    }
    public function getAllOrderItems()
    {
        $result = $this->_db_table->fetchAll(null,array('order_item_id ASC'));
        if( count($result) == 0 ) {
                return false;
        }
        $order_item_object_arr = array();
        foreach ($result as $row)
        {
                $order_item_object = new Application_Model_OrderItems($row);
                array_push($order_item_object_arr,$order_item_object);
        }
        return $order_item_object_arr;
    }
    
    public function getOrderItemByOrderId($order_id) {
        
        $query = "select order_product_id, order_id, order_product_name, order_category_name, unit_price, total_price, service_name as order_service_name, quantity
                        from order_products left join services_master services on services.service_id = order_products.order_service_name where order_id = $order_id ";
        $stmt = $this->_db_table->getAdapter()->query($query);
        $result = $stmt->fetchAll();
        
//        $where = array("order_id =?" => $order_id);
//        $result = $this->_db_table->fetchAll($where);
        if (count($result) == 0) {
            return false;
        }
        $order_item_arr = array();
        foreach ($result as $row) {
//            $prices = new Application_Model_ItemPrice();
//            foreach ($row as $key => $value) {
//                $prices->__set($key, $value);
//            }
//            $price_arr_obj[] = $prices;
            $order_item_object = new Application_Model_OrderItems();
            foreach ($row as $key => $value) {
                $order_item_object->__set($key,$value);
            }
            
            $order_item_arr[] = $order_item_object;
        }

        return $order_item_arr;
    }
    
    public function updateOrderItem(Application_Model_OrderItems $order_item)
    {
        $data = array(
	'order_id' => $order_item->__get("order_id"),
	'item_id' => $order_item->__get("item_id"),
	'quantity' => $order_item->__get("quantity"),
	'unit_price' => $order_item->__get("unit_price"),
	'total_price' => $order_item->__get("total_price"),
	'timestamp' => $order_item->__get("timestamp"),
	);
        $where = "order_item_id = " . $order_item->__get("order_item_id");
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
    public function deleteOrderItemById($order_item_id)
    {
        $where = "order_item_id = " . $order_item_id;
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
