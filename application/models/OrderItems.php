<?php 

class Application_Model_OrderItems {

    private $order_item_id;
//    private $item_id;
    private $order_id;
    private $order_product_name;
    private $order_service_name;
    private $order_category_name;
    private $package_id;
    private $quantity;
    private $unit_price;
    private $total_price;
    private $timestamp;
    

    public function __construct($order_item_row = NULL) {
        if (!is_null($order_item_row)) {
            $this->order_item_id = $order_item_row->order_item_id;
//            $this->item_id = $order_item_row->item_id;
            $this->order_id = $order_item_row->order_id;
            $this->order_product_name = $order_item_row->order_product_name;
            $this->order_service_name = $order_item_row->order_service_name;
            $this->order_category_name = $order_item_row->order_category_name;
            $this->quantity = $order_item_row->quantity;
            $this->package_id = $order_item_row->package_id;
            $this->unit_price = $order_item_row->unit_price;
            $this->total_price = $order_item_row->total_price;
//            $this->timestamp = $order_item_row->timestamp;
        }
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name) {
        return $this->$name;
    }

}
