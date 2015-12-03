<?php

namespace Application\V1\Rest\Package;

class PackageEntity {

    public $package_id;
    public $package_name;
    public $package_service_type;
    public $package_price;
    public $package_icon;
    public $no_of_clothes;
    public $saving_percent;
    public $no_of_pickups;
    public $validity;
    public $time_constraint_type;
    public $time_constraint_pickup;

    public function getArrayCopy() {
        return array(
            'package_id' => $this->package_id,
            'package_name' => $this->package_name,
            'package_service_type' => $this->package_service_type,
            'package_price' => $this->package_price,
            'package_icon' => $this->package_icon,
            'no_of_clothes' => $this->no_of_clothes,
            'saving_percent' => $this->saving_percent,
            'no_of_pickups' => $this->no_of_pickups,
            'time_constraint_type'=>$this->time_constraint_type,
            'time_constraint_pickup'=>$this->time_constraint_pickup
        );
    }

    public function exchangeArray(array $array) {
        $this->package_id = $array['package_id'];
        $this->package_name = $array['package_name'];
        $this->package_service_type = $array['package_service_type'];
        $this->package_price = $array['package_price'];
        $this->package_icon = $array['package_icon'];
        $this->no_of_clothes = $array['no_of_clothes'];
        $this->saving_percent = $array['saving_percent'];
        $this->no_of_pickups = $array['no_of_pickups'];
            }

}
