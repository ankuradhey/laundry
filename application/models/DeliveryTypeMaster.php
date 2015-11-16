<?php 

class Application_Model_DeliveryTypeMaster {

    private $delivery_type_id;
    private $delivery_type_name;
   
    public function __construct($delivery_row = NULL) {
        if (!is_null($delivery_row)) {
            
            $this->delivery_type_id = $delivery_row->delivery_type_id;
            $this->delivery_type_name = $delivery_row->delivery_type_name;
        }
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name) {
        return $this->$name;
    }

}
