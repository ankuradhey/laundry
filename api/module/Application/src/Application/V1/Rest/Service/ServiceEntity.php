<?php

namespace Application\V1\Rest\Service;

class ServiceEntity {

    public $service_id;
    public $service_name;
    public $service_status;
    public $service_image;
    public $service_icon;

    public function getArrayCopy() {
        return array(
            'service_id' => $this->service_id,
            'service_name' => $this->service_name,
            'service_status' => $this->service_status,
            'service_image' => $this->service_image,
            'service_icon' => $this->service_icon,
        );
    }

    public function exchangeArray(array $array) {
        $this->service_id = $array['service_id'];
        $this->service_name = $array['service_name'];
        $this->service_status = $array['service_status'];
        $this->service_image = $array['service_image'];
        $this->service_icon = $array['service_icon'];
    }

}
