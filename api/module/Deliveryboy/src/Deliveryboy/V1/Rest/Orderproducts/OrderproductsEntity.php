<?php

namespace Deliveryboy\V1\Rest\Orderproducts;

class OrderproductsEntity {

    public $order_product_id;
    public $order_id;
    public $order_product_name;
    public $order_item_id;
    public $order_offer_id;
    public $order_type;
    public $package_id;
    public $unit_price;
    public $total_price;
    public $quantity;

    public function getArrayCopy() {
        return array(
            'order_product_id' => $this->order_product_id,
            'order_id' => $this->order_id,
            'order_product_name' => $this->order_product_name,
            'order_item_id' => $this->order_item_id,
            'order_offer_id' => $this->order_offer_id,
            'order_type' => $this->order_type,
            'package_id' => $this->package_id,
            'unit_price' => $this->unit_price,
            'total_price' => $this->total_price,
            'quantity' => $this->quantity,
        );
    }

    public function exchangeArray(array $array) {
        $this->order_product_id = $array['order_product_id'];
        $this->order_id = $array['order_id'];
        $this->order_product_name = $array['order_product_name'];
        $this->order_item_id = $array['order_item_id'];
        $this->order_offer_id = $array['order_offer_id'];
        $this->order_type = $array['order_type'];
        $this->package_id = $array['package_id'];
        $this->unit_price = $array['unit_price'];
        $this->total_price = $array['total_price'];
        $this->quantity = $array['quantity'];
    }

}
