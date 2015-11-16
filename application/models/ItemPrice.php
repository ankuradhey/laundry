<?php 

class Application_Model_ItemPrice {

    private $item_price_id;
    private $item_id;
    private $service_id;
    private $delivery_type_name;
    private $price;
    

    public function __construct($itemprice_row = NULL) {
        if (!is_null($itemprice_row)) {
            
            $this->item_price_id = $itemprice_row->item_price_id;
            $this->item_id = $itemprice_row->item_id;
            $this->service_id = $itemprice_row->service_id;
            $this->delivery_type_name = $itemprice_row->delivery_type_name;
            $this->price = $itemprice_row->price;
            
        }
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name) {
        return $this->$name;
    }

}
