<?php

namespace Application\V1\Rest\Rate;

class RateEntity {

    public $item_price_id;
    public $item_id;
    public $service_id;
    public $item_city_id;
    public $delivery_type_name;
    public $price;

    public function getArrayCopy() {

        return array(
            'item_price_id' => $this->item_price_id,
            'item_id' => $this->item_id,
            'service_id' => $this->service_id,
            'item_city_id' => $this->item_city_id,
            'delivery_type_name' => $this->delivery_type_name,
            'price' => $this->price
        );
    }

    public function exchangeArray(array $array) {

        $this->item_price_id = $array['item_price_id'];
        $this->item_id = $array['item_id'];
        $this->service_id = $array['service_id'];
        $this->item_city_id = $array['item_city_id'];
        $this->delivery_type_name = $array['delivery_type_name'];
        $this->price = $array['price'];
    }

}
