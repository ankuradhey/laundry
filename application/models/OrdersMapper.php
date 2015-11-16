<?php 
class Application_Model_OrdersMapper {

    protected $_db_table;

    public function __construct() {
        $this->_db_table = new Application_Model_DbTable_Orders();
    }

    public function addNewOrder(Application_Model_Orders $order) {
        $data = array(
        'order_id' => $order->__get("order_id"),
        'order_user_id' => $order->__get("order_user_id"),
        'order_first_name' => $order->__get("order_first_name"),
        'order_last_name' => $order->__get("order_last_name"),
        'order_user_email' => $order->__get("order_user_email"),
        'order_address' => $order->__get("order_address"),
        'order_address_additional' => $order->__get("order_address_additional"),
//        'order_phone' => $order->__get("order_phone"),
        'order_city' => $order->__get("order_city"),
        'order_pincode' => $order->__get("order_pincode"),
        'order_delivery_note' => $order->__get("order_delivery_note"),
        'order_delivery_type' => $order->__get("order_delivery_type"),
        'order_pickup' => $order->__get("order_pickup"),
        'order_delivery' => $order->__get("order_delivery"),
        'order_pickup_time' => $order->__get("order_pickup_time"),
        'order_delivery_time' => $order->__get("order_delivery_time"),
        'order_amount' => $order->__get("order_amount"),
        'order_payment_type' => $order->__get("order_payment_type"),
        'delivery_charge' => $order->__get("delivery_charge"),
        'service_tax' => $order->__get("service_tax"),
        'order_service_type' => $order->__get("order_service_type"),
        'order_type' => $order->__get("order_type"),
        'order_payment_status' => $order->__get("order_payment_status"),
        'order_status' => $order->__get("order_status"),
        'order_delivery_boy' => $order->__get("order_delivery_boy"),
        'order_added_time' => $order->__get("order_added_time"),
        'order_modified_time' => $order->__get("order_modified_time"),
        );
        
        if(empty($data['order_added_time']))
            unset($data['order_added_time']);
            
        $result = $this->_db_table->insert($data);
        if (count($result) == 0) {
            return false;
        } else {
            return $result;
        }
    }

    public function getOrderById($order_id) {
        $result = $this->_db_table->find($order_id);
        if (count($result) == 0) {
            return false;
        }
        $row = $result->current();
        $order = new Application_Model_Orders($row);
        return $order;
    }

    public function getAllOrders() {
        $result = $this->_db_table->fetchAll(null, array('order_id ASC'));
        if (count($result) == 0) {
            return false;
        }
        $order_object_arr = array();
        foreach ($result as $row) {
            $order_object = new Application_Model_Orders($row);
            array_push($order_object_arr, $order_object);
        }
        return $order_object_arr;
    }

    public function getOrdersByStatus() {
        $query = "SELECT * FROM `orders` WHERE `order_status`is NULL ORDER BY `order_id` DESC";

        $stmt = $this->_db_table->getAdapter()->query($query);
        $result = $stmt->fetchAll();
        if (count($result) == 0) {
            return false;
        }
        $order_arr_obj = array();
        foreach ($result as $row) {
            $orders = new Application_Model_Orders();
            foreach ($row as $key => $value) {
                $orders->__set($key, $value);
            }
            $order_arr_obj[] = $orders;
        }
        return $order_arr_obj;
    }

    public function getOrdersByStatusUserEmail($user_email) {
        $query = "SELECT * FROM `orders` WHERE `order_status`='Delivered' AND `user_email`='" . $user_email . "' ORDER BY `order_id` DESC";

        $stmt = $this->_db_table->getAdapter()->query($query);
        $result = $stmt->fetchAll();
        if (count($result) == 0) {
            return false;
        }
        $order_arr_obj = array();
        foreach ($result as $row) {
            $orders = new Application_Model_Orders();
            foreach ($row as $key => $value) {
                $orders->__set($key, $value);
            }
            $order_arr_obj[] = $orders;
        }
        return $order_arr_obj;
    }
    
    public function getOrdersByUserId($user_id) {
        $query = "SELECT * FROM `orders` WHERE `order_user_id`='" . $user_id . "' ORDER BY `order_id` DESC";

        $stmt = $this->_db_table->getAdapter()->query($query);
        $result = $stmt->fetchAll();
        if (count($result) == 0) {
            return false;
        }
        $order_arr_obj = array();
        foreach ($result as $row) {
            $orders = new Application_Model_Orders();
            foreach ($row as $key => $value) {
                $orders->__set($key, $value);
            }
            $order_arr_obj[] = $orders;
        }
        return $order_arr_obj;
    }

    public function getOrdersByOtherStatusUserEmail($user_email) {
        $query = "SELECT * FROM `orders` WHERE `order_status`!='Delivered' AND `user_email`='" . $user_email . "' ORDER BY `order_id` DESC";

        $stmt = $this->_db_table->getAdapter()->query($query);
        $result = $stmt->fetchAll();
        if (count($result) == 0) {
            return false;
        }
        $order_arr_obj = array();
        foreach ($result as $row) {
            $orders = new Application_Model_Orders();
            foreach ($row as $key => $value) {
                $orders->__set($key, $value);
            }
            $order_arr_obj[] = $orders;
        }
        return $order_arr_obj;
    }

    public function updateOrder(Application_Model_Orders $order) {
        $data = array(
            'user_fname' => $order->__get("user_fname"),
            'user_lname' => $order->__get("user_lname"),
            'user_email' => $order->__get("user_email"),
            'user_phn_number' => $order->__get("user_phn_number"),
            'user_address' => $order->__get("user_address"),
            'address_locality' => $order->__get("address_locality"),
            'address_street' => $order->__get("address_street"),
            'address_landmark' => $order->__get("address_landmark"),
            'address_city' => $order->__get("address_city"),
            'address_state' => $order->__get("address_state"),
            'address_pincode' => $order->__get("address_pincode"),
            'address_country' => $order->__get("address_country"),
            'payment_method' => $order->__get("payment_method"),
            'payment_status' => $order->__get("payment_status"),
            'order_status' => $order->__get("order_status"),
            'order_total' => $order->__get("order_total"),
            'discount_price' => $order->__get("discount_price"),
            'revised_price' => $order->__get("revised_price"),
            'timestamp' => $order->__get("timestamp"),
            'delivery_type' => $order->__get("delivery_type"),
            'service' => $order->__get("service"),
            'pickup_date' => $order->__get("pickup_date"),
            'pickup_time' => $order->__get("pickup_time"),
            'delivery_date' => $order->__get("delivery_date"),
            'delivery_time' => $order->__get("delivery_time"),
        );
        $where = "order_id = " . $order->__get("order_id");
        $result = $this->_db_table->update($data, $where);
        if (count($result) == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function filter($user_email, $del_type, $service_id, $del_date, $pickup_date) {
        //echo $date; exit;
        $query = "SELECT * FROM `orders` WHERE ";

        //if ($type == "index") {
        //$query .= "`order_status`='recieved' AND";
        //}
        //if ($type == "summary") {
        //$query .= "`order_status` !='recieved' AND";
        //}

        if (!empty($user_email)) {
            $query .= " `user_email` = '" . $user_email . "' AND";
        }

        if (!empty($del_type)) {
            $query .= "`delivery_type` = '" . $del_type . "' AND";
        }
        if (!empty($service_id)) {
            $query .= "`service` = '" . $service_id . "' AND";
        }
        if (!empty($del_date)) {
            $query .= "`delivery_date` = '" . $del_date . "' AND";
        }
        if (!empty($pickup_date)) {
            $query .= "`pickup_date` = '" . $pickup_date . "' AND";
        }

        $query = substr($query, 0, -4);
        $query .= " ORDER BY order_id DESC";
        // echo $query;
        //exit;

        $stmt = $this->_db_table->getAdapter()->query($query);
        $result = $stmt->fetchAll();
        if (count($result) == 0) {
            $orders = new Application_Model_Orders();
            return false;
        }
        $order_arr_obj = array();
        foreach ($result as $row) {
            $orders = new Application_Model_Orders();
            foreach ($row as $key => $value) {
                $orders->__set($key, $value);
            }
            $order_arr_obj[] = $orders;
        }
        return $order_arr_obj;
    }

    public function deleteOrderById($order_id) {
        $where = "order_id = " . $order_id;
        $result = $this->_db_table->delete($where);
        if (count($result) == 0) {
            return false;
        } else {
            return true;
        }
    }
    
    public function allotOrder(Application_Model_Orders $order) {
        $data = array(
            'order_status' => $order->__get("order_status"),
            'order_delivery_boy' => $order->__get("order_delivery_boy"),
        );
        $where = "order_id = " . $order->__get("order_id");
        $result = $this->_db_table->update($data, $where);
        if (count($result) == 0) {
            return false;
        } else {
            return true;
        }
    }
}
