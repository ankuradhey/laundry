<?php

class Application_Model_Orders {

    private $order_id;
    private $order_user_id;
    private $order_first_name;
    private $order_last_name;
    private $order_user_email;
    private $order_address;
    private $order_address_additional;
//    private $order_phone;
    private $order_city;
    private $order_pincode;
    private $order_delivery_note;
    private $order_delivery_type;
    private $order_pickup;
    private $order_delivery;
    private $order_delivery_time;
    private $order_pickup_time;
    private $order_amount;
    private $order_payment_type;
    private $delivery_charge;
    private $service_tax;
    private $order_service_type;
    private $order_type;
    private $order_payment_status;
    private $order_status;
    private $order_delivery_boy;
    private $order_added_time;
    private $order_modified_time;
    

    public function __construct($order_row = NULL) {
        if (!is_null($order_row)) {

            $this->order_id = $order_row->order_id;
            $this->order_user_id = $order_row->order_user_id;
            $this->order_first_name = $order_row->order_first_name;
            $this->order_last_name = $order_row->order_last_name;
            $this->order_user_email = $order_row->order_user_email;
            $this->order_address = $order_row->order_address;
            $this->order_address_additional = $order_row->order_address_additional;
//            $this->order_phone = $order_row->order_phone;
            $this->order_city = $order_row->order_city;
            $this->order_pincode = $order_row->order_pincode;
            $this->order_delivery_note = $order_row->order_delivery_note;
            $this->order_delivery_type = $order_row->order_delivery_type;
            $this->order_pickup = $order_row->order_pickup;
            $this->order_delivery = $order_row->order_delivery;
            $this->order_pickup_time = $order_row->order_pickup_time;
            $this->order_delivery_time = $order_row->order_delivery_time;
            $this->order_amount = $order_row->order_amount;
            $this->order_payment_type = $order_row->order_payment_type;
            $this->delivery_charge = $order_row->delivery_charge;
            $this->service_tax = $order_row->service_tax;
            $this->order_service_type = $order_row->order_service_type;
            $this->order_type = $order_row->order_type;
            $this->order_payment_status = $order_row->order_payment_status;
            $this->order_status = $order_row->order_status;
            $this->order_delivery_boy = $order_row->order_delivery_boy;
            $this->order_added_time = $order_row->order_added_time;
            $this->order_modified_time = $order_row->order_modified_time;
        }
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name) {
        return $this->$name;
    }

}
