<?php

class Application_Model_ServiceMasters {

    private $service_id;
    private $service_name;
    private $service_status;
    private $service_image;

    public function __construct($service_row = NULL) {

        if (!is_null($service_row)) {

            $this->service_id = $service_row->service_id;
            $this->service_name = $service_row->service_name;
            $this->service_status = $service_row->service_status;
            $this->service_image = $service_row->service_image;
            $this->service_icon = $service_row->service_icon;
        }
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name) {
        return $this->$name;
    }

}
