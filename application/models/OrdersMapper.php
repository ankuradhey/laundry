<?php 
class Application_Model_OrdersMapper {

    protected $_db_table;
    protected $timeSlot;
    
    public function __construct(){
		
        $this->_db_table = new Application_Model_DbTable_Orders();
        $this->timeSlot = array('9:00AM-11:00AM','11:00AM-1:00PM','1:00PM-3:00PM','3:00PM-5:00PM','5:00PM-7:00PM','7:00PM-9:00PM','9:00PM-11:00PM');
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
        'order_pickup_boy' => $order->__get("order_pickup_boy"),
        'order_added_time' => $order->__get("order_added_time"),
        'order_modified_time' => $order->__get("order_modified_time"),
		
		'order_mobile_number' => $order->__get("order_mobile_number"),
		'order_coupon_id' => $order->__get("order_coupon_id"),
		'order_coupon_dis' => $order->__get("order_coupon_dis"),
				
        );
        
        if(empty($data['order_added_time']))
            unset($data['order_added_time']);
        
        foreach($data as $key=>$val){
            if(empty($val)){
                unset($data[$key]);
            }
        }
        
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
		
		$stmt = $this->_db_table->getAdapter()
						->select()->from("orders")
						->join("delivery_types_master","delivery_type_id = order_delivery_type")
						->order('order_id ASC');

        $result = $stmt->query()->fetchAll();

        if (count($result) == 0) {
            return false;
        }
		
		
        $order_object_arr = array();
        foreach ($result as $row) {
			//$row['order_delivery_type'] = $row['delivery_type_name'];
            $order_object = new Application_Model_Orders((object)$row);
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
            $query .= " `user_email` = '" . $user_email . "' AND ";
        }

//        if (!empty($del_type)) {
//            $query .= "`order_delivery_type` = " . $del_type . " AND ";
//        }
        if (!empty($service_id)) {
            $query .= "`service` = '" . $service_id . "' AND ";
        }
        if (!empty($del_date)) {
//            $query .= "`order_delivery` = '" . date("Y-m-d",strtotime($del_date)) . " 00:00:00' AND ";
        }
        
        if (!empty($del_type)) {
            if($del_type == 'not_picked')
                $query .= " (`order_status` is NULL or `order_status` = 'alloted' ) AND ";
            elseif($del_type == 'not_delivered')
                $query .= " (`order_status` is NULL or `order_status` != 'delivered' ) AND ";
        }
        
//        $query .= " `order_delivery` = '" . date("Y-m-d",strtotime($del_date)) . " 00:00:00' AND ";

        $query = substr($query, 0, -4);
        $query .= " ORDER BY order_id DESC";
//         echo $query;
//        exit;

        $stmt = $this->_db_table->getAdapter()->query($query);
        $result = $stmt->fetchAll();
		
        /*if (count($result) == 0) {
            $orders = new Application_Model_Orders();
            return false;
        }*/
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
    
    public function allotOrder(Application_Model_Orders $order, $orderType = 'pickup') {
        $data = array(
            'order_status' => $order->__get("order_status"),
            
        );
        
        if($orderType == 'pickup'){
            $data['order_pickup_boy'] =  $order->__get("order_pickup_boy");
        }else{
            $data['order_delivery_boy'] =  $order->__get("order_delivery_boy");
        }
        
        $where = "order_id = " . $order->__get("order_id");
        $result = $this->_db_table->update($data, $where);
        if (count($result) == 0) {
            return false;
        } else {
            return true;
        }
    }
	
	public function getOrders($params = array()) {
						
        $result = $this->_db_table->getAdapter()
                ->select()
                ->from("orders")																		
                ->where("order_user_id = ?",$params['user_id'])
                ->where("order_coupon_id = ?",$params['coupon_id'])
                ->query()
                ->fetchAll();
				
		if(isset($params['order_count']) && $params['order_count']){
			
			return count($result);
			
		}
    }
    
    public function getOrderByDeliveryBoy($orderDelivery = null, $orderPickup = null, $type = 'delivery'){
        $result = $this->_db_table->getAdapter()
                ->select()
                ->from("orders");
        
        
                
        
        if($orderPickup){
            //TO DO COmmented temporary
//            $result->where(" order_status is NULL and order_pickup = '".$orderPickup." 00:00:00' ");
            $result->join("delivery_boy"," delivery_boy.delboy_id = orders.order_pickup_boy ");
//            $result->where(" order_status is NULL and order_pickup = '".$orderPickup." 00:00:00' ");
            $result->order("order_pickup_time asc");
        }
        elseif($orderDelivery){
            //TO DO COmmented temporary
            $result->join("delivery_boy"," delivery_boy.delboy_id = orders.order_delivery_boy ");
//            $result->where(" order_status = 'picked' and order_delivery = '".$orderDelivery." 00:00:00' ");
//            $result->where(" order_status = 'picked' and order_delivery = '".$orderDelivery." 00:00:00' ");
            $result->order("order_delivery_time asc");
        }
        
        $result = $result->query()->fetchAll();
        $resultArr = array();
        $count = array();
        foreach($result as $key=>$val){
            if($orderPickup){
                $resultArr[$val['delboy_id']][$val['order_pickup_time']][] = $val;
                $resultArr[$val['delboy_id']]['info']['orderscount'] = count($resultArr[$val['delboy_id']][$val['order_pickup_time']]);
            }elseif($orderDelivery){
                $resultArr[$val['delboy_id']][$val['order_delivery_time']][] = $val;
                $resultArr[$val['delboy_id']]['info']['orderscount'] = count($resultArr[$val['delboy_id']][$val['order_delivery_time']]);
            }
            $resultArr[$val['delboy_id']]['info']['name'] = $val['delboy_fname'].' '.$val['delboy_lname'];
            $resultArr[$val['delboy_id']]['info']['id'] = $val['delboy_id'];
            
        }
        return $resultArr;
        
    }
    
    public function getTimeSlot(){
        return $this->timeSlot;
    }
	
}
