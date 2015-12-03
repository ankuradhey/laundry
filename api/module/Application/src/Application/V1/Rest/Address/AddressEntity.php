<?php

namespace Application\V1\Rest\Address;

class AddressEntity {

    public $addr_id;
    public $addr_user_id;
    public $addr_label;
    public $addr_default;
    public $addr_flat_no;
    public $addr_street;
    public $addr_city;
    public $addr_pincode;
    public $addr_modified_time;

    public function getArrayCopy() {
        return array(
            'addr_id' => $this->addr_id,
            'addr_user_id' => $this->addr_user_id,
            'addr_label' => $this->addr_label,
            'addr_default' => $this->addr_default,
            'addr_flat_no' => $this->addr_flat_no,
            'addr_street' => $this->addr_street,
            'addr_city' => $this->addr_city,
            'addr_pincode' => $this->addr_pincode,
            'addr_modified_time' => $this->addr_modified_time,
        );
    }

    public function exchangeArray(array $array) {
        $this->addr_id = $array['addr_id'];
        $this->addr_user_id = $array['addr_user_id'];
        $this->addr_label = $array['addr_label'];
        $this->addr_default = $array['addr_default'];
        $this->addr_flat_no = $array['addr_flat_no'];
        $this->addr_street = $array['addr_street'];
        $this->addr_city = $array['addr_city'];
        $this->addr_pincode = $array['addr_pincode'];
        $this->addr_modified_time = $array['addr_modified_time'];
    }

}
