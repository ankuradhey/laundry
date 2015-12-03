<?php

namespace Application\V1\Rest\Offer;

class OfferEntity {

    public $offer_id;
    public $offer_name;
    public $offer_clothes;
    public $offer_amount;
    public $no_of_clothes;
    public $no_of_pickups;

    public function getArrayCopy() {
        return array(
            'offer_id' => $this->offer_id,
            'offer_name' => $this->offer_name,
            'offer_clothes' => $this->offer_clothes,
            'offer_amount' => $this->offer_amount,
            'no_of_clothes' => $this->no_of_clothes,
            'no_of_pickups' => $this->no_of_pickups,
        );
    }

    public function exchangeArray(array $array) {
        $this->offer_id = $array['offer_id'];
        $this->offer_name = $array['offer_name'];
        $this->offer_clothes = $array['offer_clothes'];
        $this->offer_amount = $array['offer_amount'];
        $this->no_of_clothes = $array['no_of_clothes'];
        $this->no_of_pickups = $array['no_of_pickups'];
    }

}
