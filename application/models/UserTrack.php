<?php

class Application_Model_UserTrack {

    private $usertrack_id;
    private $usertrack_user_id;
    private $track_type;
    private $usertrack_package_id;
    private $usertrack_offer_id;
    private $clothes_left;
    private $pickups_left;
    private $pickups_availed;
    private $usertrack_start_date;
    private $usertrack_expiry_date;
    private $usertrack_house;
    private $usertrack_locality;
    private $usertrack_city;
    private $package_name;
    private $package_icon;
            
    public function __construct($order_row = NULL) {
        if (!is_null($order_row)) {
            $this->usertrack_id = $order_row->usertrack_id;
            $this->usertrack_user_id = $order_row->usertrack_user_id;
            $this->track_type = $order_row->track_type;
            $this->usertrack_package_id = $order_row->usertrack_package_id;
            $this->usertrack_offer_id = $order_row->usertrack_offer_id;
            $this->clothes_left = $order_row->clothes_left;
            $this->pickups_left = $order_row->pickups_left;
            $this->pickups_availed = $order_row->pickups_availed;
            $this->usertrack_start_date = $order_row->usertrack_start_date;
            $this->usertrack_expiry_date = $order_row->usertrack_expiry_date;
            $this->usertrack_house = $order_row->usertrack_house;
            $this->usertrack_locality = $order_row->usertrack_locality;
            $this->usertrack_city = $order_row->usertrack_city;
        }
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name) {
        return $this->$name;
    }

}