<?php 

class Application_Model_Promotions {

    private $offer_id;
    private $offer_name;
	private $offer_service_type;
    private $offer_clothes;
    private $offer_amount;
    private $offer_status;
	private $no_of_clothes;
	private $no_of_pickups;
    

    public function __construct($offer_row = NULL) {
        if (!is_null($offer_row)) {
            $this->offer_id = $offer_row->offer_id;
            $this->offer_name = $offer_row->offer_name;
			
			$this->offer_service_type = $offer_row->offer_service_type;
            $this->offer_clothes = $offer_row->offer_clothes;
			
            $this->no_of_clothes = $offer_row->no_of_clothes;
			$this->no_of_pickups = $offer_row->no_of_pickups;
			
            $this->offer_amount = $offer_row->offer_amount;
            $this->offer_status = $offer_row->offer_status;
        }
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name) {
        return $this->$name;
    }

}
