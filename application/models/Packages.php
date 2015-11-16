<?php 

class Application_Model_Packages {

    private $package_id;
    private $package_name;
    private $package_price;
    private $package_icon;
    private $no_of_clothes;
    private $saving_percent;
    private $validity;
    private $no_of_pickups;
    private $package_service_type;
    

    public function __construct($package_row = NULL) {
        if (!is_null($package_row)) {
            
            $this->package_id = $package_row->package_id;
            $this->package_name = $package_row->package_name;
            $this->package_price = $package_row->package_price;
            $this->package_icon = $package_row->package_icon;
            $this->no_of_clothes = $package_row->no_of_clothes;
            $this->saving_percent = $package_row->saving_percent;
            $this->validity = $package_row->validity;
            $this->no_of_pickups = $package_row->no_of_pickups;
            $this->package_service_type = $package_row->package_service_type;
            
        }
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name) {
        return $this->$name;
    }

}
