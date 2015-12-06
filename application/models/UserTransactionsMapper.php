<?php

class Application_Model_UserTransactionsMapper {

    protected $_db_table;

    public function __construct() {
        $this->_db_table = new Application_Model_DbTable_UserTransactions();
    }

    public function addNewUserTransaction(Application_Model_UserTransactions $user_transaction) {
        $data = array(
            'trnx_order_id' => $user_transaction->__get("trnx_order_id"),
            'trnx_user_id' => $user_transaction->__get("trnx_user_id"),
            'other_details' => $user_transaction->__get("other_details"),
            'gateway_transaction_id' => $user_transaction->__get("gateway_transaction_id"),
            'trnx_amount' => $user_transaction->__get("trnx_amount"),
            'trnx_method' => $user_transaction->__get("trnx_method"),
            'trnx_status' => $user_transaction->__get("trnx_status"),
            'added_timestamp' => $user_transaction->__get("added_timestamp"),
        );
        $result = $this->_db_table->insert($data);
        if (count($result) == 0) {
            return false;
        } else {
            return $result;
        }
    }

    public function getUserTransactionById($user_transaction_id) {
        $result = $this->_db_table->find($user_transaction_id);
        if (count($result) == 0) {
            return false;
        }
        $row = $result->current();
        $user_transaction = new Application_Model_UserTransactions($row);
        return $user_transaction;
    }

    public function getAllUserTransactions() {
        $result = $this->_db_table->fetchAll(null, array('trnx_id ASC'));
        if (count($result) == 0) {
            return false;
        }
        $user_transaction_object_arr = array();
        foreach ($result as $row) {
            $user_transaction_object = new Application_Model_UserTransactions($row);
            array_push($user_transaction_object_arr, $user_transaction_object);
        }
        return $user_transaction_object_arr;
    }

    public function updateUserTransaction(Application_Model_UserTransactions $user_transaction) {
        $data = array(
            'trnx_order_id' => $user_transaction->__get("trnx_order_id"),
            'trnx_user_id' => $user_transaction->__get("trnx_user_id"),
            'other_details' => $user_transaction->__get("other_details"),
            'gateway_transaction_id' => $user_transaction->__get("gateway_transaction_id"),
            'trnx_amount' => $user_transaction->__get("trnx_amount"),
            'trnx_method' => $user_transaction->__get("trnx_method"),
            'trnx_status' => $user_transaction->__get("trnx_status"),
            'added_timestamp' => $user_transaction->__get("added_timestamp"),
        );
        $where = "trnx_id = " . $user_transaction->__get("trnx_id");
        $result = $this->_db_table->update($data, $where);
        if (count($result) == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function deleteUserTransactionById($user_transaction_id) {
        $where = "trnx_user_id = " . $user_transaction_id;
        $result = $this->_db_table->delete($where);
        if (count($result) == 0) {
            return false;
        } else {
            return true;
        }
    }

}
