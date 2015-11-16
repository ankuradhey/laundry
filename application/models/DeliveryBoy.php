<?php 

class Application_Model_DeliveryBoy {

    private $delboy_id;
    private $delboy_fname;
    private $delboy_lname;
    private $delboy_address;
    private $delboy_username;
    private $delboy_password;
    private $delboy_status;
   
    public function __construct($delivery_row = NULL) {
        if (!is_null($delivery_row)) {
            $this->delboy_id = $delivery_row->delboy_id;
            $this->delboy_fname = $delivery_row->delboy_fname;
            $this->delboy_lname = $delivery_row->delboy_lname;
            $this->delboy_address = $delivery_row->delboy_address;
            $this->delboy_username = $delivery_row->delboy_username;
            $this->delboy_password = $delivery_row->delboy_password;
            $this->delboy_status = $delivery_row->delboy_status;
        }
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name) {
        return $this->$name;
    }

}
