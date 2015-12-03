<?php

namespace Application\V1\Rest\Order;

class OrderEntity {

    public $order_id;
    public $order_user_id;
    public $order_delivery_type;
    public $order_first_name;
    public $order_last_name;
    public $order_user_email;
    public $order_address;
    public $order_address_additional;
    public $order_city;
    public $order_pincode;
    public $order_pickup;
    public $order_delivery;
    public $order_amount;
    public $order_payment_type;
    public $order_service_type;
    public $order_payment_status;
    public $order_status;
    public $order_delivery_boy;
    public $order_modified_time;
    public $order_type;

    public function getArrayCopy() {
        return array(
            'order_id' => $this->order_id,
            'order_user_id' => $this->order_user_id,
            'order_delivery_type' => $this->order_delivery_type,
            'order_first_name' => $this->order_first_name,
            'order_last_name' => $this->order_last_name,
            'order_user_email' => $this->order_user_email,
            'order_address' => $this->order_address,
            'order_address_additional' => $this->order_address_additional,
            'order_city' => $this->order_city,
            'order_pincode' => $this->order_pincode,
            'order_pickup' => $this->order_pickup,
            'order_delivery' => $this->order_delivery,
            'order_amount' => $this->order_amount,
            'order_payment_type' => $this->order_payment_type,
            'order_service_type' => $this->order_service_type,
            'order_payment_status' => $this->order_payment_status,
            'order_status' => $this->order_status,
            'order_delivery_boy' => $this->order_delivery_boy,
            'order_modified_time' => $this->order_modified_time,
            'order_type' => $this->order_type,
        );
    }

    public function exchangeArray(array $array) {
        $this->order_id = $array['order_id'];
        $this->order_user_id = $array['order_user_id'];
        $this->order_delivery_type = $array['order_delivery_type'];
        $this->order_first_name = $array['order_first_name'];
        $this->order_last_name = $array['order_last_name'];
        $this->order_user_email = $array['order_user_email'];
        $this->order_address = $array['order_address'];
        $this->order_address_additional = $array['order_address_additional'];
        $this->order_city = $array['order_city'];
        $this->order_pincode = $array['order_pincode'];
        $this->order_pickup = $array['order_pickup'];
        $this->order_delivery = $array['order_delivery'];
        $this->order_amount = $array['order_amount'];
        $this->order_payment_type = $array['order_payment_type'];
        $this->order_service_type = $array['order_service_type'];
        $this->order_payment_status = $array['order_payment_status'];
        $this->order_status = $array['order_status'];
        $this->order_delivery_boy = $array['order_delivery_boy'];
        $this->order_modified_time = $array['order_modified_time'];
        $this->order_type = $array['order_type'];
    }

}
