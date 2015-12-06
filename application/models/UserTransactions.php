<?php 

class Application_Model_UserTransactions {

    private $trnx_id;
    private $trnx_order_id;
    private $trnx_user_id;
    private $other_details;
    private $gateway_transaction_id;
    private $trnx_amount;
    private $trnx_method;
    private $trnx_status;
    private $added_timestamp;
    
    public function __construct($user_transaction_row = NULL) {
        if (!is_null($user_transaction_row)) {
            
            $this->trnx_id = $user_transaction_row->trnx_id;
            $this->trnx_order_id = $user_transaction_row->trnx_order_id;
            $this->trnx_user_id = $user_transaction_row->trnx_user_id;
            $this->other_details = $user_transaction_row->other_details;
            $this->gateway_transaction_id = $user_transaction_row->gateway_transaction_id;
            $this->trnx_amount = $user_transaction_row->trnx_amount;
            $this->trnx_method = $user_transaction_row->trnx_method;
            $this->trnx_status = $user_transaction_row->trnx_status;
            $this->added_timestamp = $user_transaction_row->added_timestamp;

        }
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name) {
        return $this->$name;
    }

}
