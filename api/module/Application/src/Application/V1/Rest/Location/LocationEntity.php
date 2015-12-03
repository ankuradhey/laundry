<?php
namespace Application\V1\Rest\Location;

class LocationEntity
{
    public $city_id;
    public $city_name;
    public $local_area_id;
    public $local_area_name;

    public function getArrayCopy() {
        return array(
            'city_id' => $this->city_id,
            'city_name' => $this->city_name,
            'local_area_id' => $this->local_area_id,
            'local_area_name' => $this->local_area_name,
        );
    }

    public function exchangeArray(array $array) {
        $this->city_id = $array['city_id'];
        $this->city_name = $array['city_name'];
        $this->local_area_id = $array['local_area_id'];
        $this->local_area_name = $array['local_area_name'];
    }

}
