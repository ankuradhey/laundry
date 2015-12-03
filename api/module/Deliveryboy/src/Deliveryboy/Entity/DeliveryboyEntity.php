<?php

namespace Deliveryboy\Entity;

class DeliveryboyEntity {

    public $delboy_id;
    public $delboy_fname;
    public $delboy_lname;
    public $delboy_address;
    public $delboy_username;
    public $delboy_password;
    public $delboy_status;

    public function getArrayCopy() {
        return array(
            'delboy_id' => $this->delboy_id,
            'delboy_fname' => $this->delboy_fname,
            'delboy_lname' => $this->delboy_lname,
            'delboy_address' => $this->delboy_address,
            'delboy_username' => $this->delboy_username,
            'delboy_password' => $this->delboy_password,
            'delboy_status' => $this->delboy_status,
        );
    }

    public function exchangeArray(array $array) {
        $this->delboy_id = $array['delboy_id'];
        $this->delboy_fname = $array['delboy_fname'];
        $this->delboy_lname = $array['delboy_lname'];
        $this->delboy_address = $array['delboy_address'];
        $this->delboy_username = $array['delboy_username'];
        $this->delboy_password = $array['delboy_password'];
        $this->delboy_status = $array['delboy_status'];
    }

}