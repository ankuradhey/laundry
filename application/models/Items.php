<?php 

class Application_Model_Items {

    private $item_id;
    private $item_name;
    private $item_order;
    private $is_live;
    private $item_description;
    private $item_image;
    private $category_id;
    private $service_id;
    

    public function __construct($item_row = NULL) {
        if (!is_null($item_row)) {
            
            $this->item_id = $item_row->item_id;
            $this->item_name = $item_row->item_name;
            $this->item_order = $item_row->item_order;
            $this->is_live = $item_row->is_live;
            $this->item_description = $item_row->item_description;
            $this->item_image = $item_row->item_image;
            $this->service_id = $item_row->service_id;
            $this->category_id = $item_row->category_id;
            
        }
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name) {
        return $this->$name;
    }

}
