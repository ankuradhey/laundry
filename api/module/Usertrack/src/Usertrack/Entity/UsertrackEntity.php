<?php

namespace Usertrack\Entity;

class UsertrackEntity {

    public $usertrack_id;
    public $usertrack_user_id;
    public $track_type;
    public $usertrack_package_id;
    public $usertrack_offer_id;
    public $usertrack_service_id;
    public $clothes_left;
    public $clothes_availed;
    public $pickups_availed;
    public $pickups_left;

    public function getArrayCopy() {
        return array(
            'usertrack_id' => $this->usertrack_id,
            'usertrack_user_id' => $this->usertrack_user_id,
            'track_type' => $this->track_type,
            'usertrack_package_id' => $this->usertrack_package_id,
            'usertrack_offer_id' => $this->usertrack_offer_id,
            'usertrack_service_id' => $this->usertrack_service_id,
            'clothes_left' => $this->clothes_left,
            'clothes_availed' => $this->clothes_availed,
            'pickups_availed' => $this->pickups_availed,
            'pickups_left' => $this->pickups_left,
        );
    }

    public function exchangeArray(array $array) {
        $this->usertrack_id = $array['usertrack_id'];
        $this->usertrack_user_id = $array['usertrack_user_id'];
        $this->track_type = $array['track_type'];
        $this->usertrack_package_id = $array['usertrack_package_id'];
        $this->usertrack_offer_id = $array['usertrack_offer_id'];
        $this->usertrack_service_id = $array['usertrack_service_id'];
        $this->clothes_left = $array['clothes_left'];
        $this->clothes_availed = $array['clothes_availed'];
        $this->pickups_availed = $array['pickups_availed'];
        $this->pickups_left = $array['pickups_left'];
    }

}
