<?php

class Application_Model_Coupons {

    private $coupon_id;
    private $coupon_code;
    private $coupon_value;
    private $coupon_type;
    private $coupon_last_date;
    private $coupon_occourence;
    private $coupon_min_billing;
    private $coupon_max_discount;
    private $coupon_status;

    public function __construct($coupon_row = NULL) {
        if (!is_null($coupon_row)) {
			
            $this->coupon_id = $coupon_row->coupon_id;
            $this->coupon_code = $coupon_row->coupon_code;
            $this->coupon_value = $coupon_row->coupon_value;
            $this->coupon_type = $coupon_row->coupon_type;
			
			$this->coupon_last_date = $coupon_row->coupon_last_date;
			$this->coupon_occourence = $coupon_row->coupon_occourence;
			$this->coupon_min_billing = ((int)$coupon_row->coupon_min_billing)<1?"":$coupon_row->coupon_min_billing;
			$this->coupon_max_discount = ((int)$coupon_row->coupon_max_discount)<1?"":$coupon_row->coupon_max_discount;
			$this->coupon_status = $coupon_row->coupon_status;
			
        }
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name) {
        return $this->$name;
    }

}
