<?php 

class Application_Model_Categories {

    private $category_id;
    private $category_name;
    private $category_order;
    private $is_live;

    public function __construct($category_row = NULL) {
        if (!is_null($category_row)) {
            $this->category_id = $category_row->category_id;
            $this->category_name = $category_row->category_name;
            $this->category_order = $category_row->category_order;
            $this->is_live = $category_row->is_live;
        }
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name) {
        return $this->$name;
    }

}
