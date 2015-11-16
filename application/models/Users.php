<?php 

class Application_Model_Users {

    private $user_id;
    private $user_fname;
    private $user_lname;
    private $user_email;
    private $hashed_password;
    private $reset_code;
    private $last_login;
    private $activation_code;
    private $timestamp;
    private $user_fb_id;
    private $user_number;
    private $user_address;
    private $user_address_additional;
    private $user_locality;
    private $user_landmark;
    private $user_city;
    private $user_state;
    private $user_country;
    private $referred_by;
    
    public function __construct($user_row = NULL) {
        if (!is_null($user_row)) {
            $this->user_id = $user_row->user_id;
            $this->user_fname = $user_row->user_fname;
            $this->user_lname = $user_row->user_lname;
            $this->user_email = $user_row->user_email;
            $this->hashed_password = $user_row->hashed_password;
            $this->activation_code = $user_row->activation_code;
            $this->timestamp = $user_row->timestamp;
            $this->reset_code = $user_row->reset_code;
            $this->user_fb_id = $user_row->user_fb_id;
            $this->last_login = $user_row->last_login;
            $this->user_number = $user_row->user_number;
            $this->user_address = $user_row->user_address;
            $this->user_address_additional = $user_row->user_address_additional;
            $this->user_locality = $user_row->user_locality;
            $this->user_landmark = $user_row->user_landmark;
            $this->user_city = $user_row->user_city;
            $this->user_state = $user_row->user_state;
            $this->user_country = $user_row->user_country;
            $this->referred_by = $user_row->referred_by;
        }
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name) {
        return $this->$name;
    }

}
