<?php

class Application_Model_PromotionsMapper {

    protected $_db_table;

    public function __construct() {
        $this->_db_table = new Application_Model_DbTable_Promotions();
    }

    public function addNewPackage(Application_Model_Promotions $offer) {
        $data = array(
            "offer_id" => $offer->__get("offer_id"),
            "offer_name" => $offer->__get("offer_name"),
            "offer_clothes" => $offer->__get("offer_clothes"),
            "offer_amount" => $offer->__get("offer_amount"),
            "offer_status" => $offer->__get("offer_status"),
        );

        $result = $this->_db_table->insert($data);
        return($result);
    }

    public function getAllOffers() {

        $result = $this->_db_table->fetchAll(NULL, array('offer_id ASC'));
        if (count($result) == 0) {
            return false;
        }
        $offers_arr = array();
        foreach ($result as $row) {
            $offer = new Application_Model_Promotions($row);
            $offers_arr[] = $offer;
        }
        return $offers_arr;
    }

    public function getPackageById($id) {
        $where = array(
            "package_id = ?" => $id
        );
        $result = $this->_db_table->fetchRow($where);
        if (!$result) {

            return FALSE;
        }
        $offer = new Application_Model_Packages($result);
        return $offer;
    }

    public function updatePackage(Application_Model_Packages $offer) {
        $data = array(
            "package_name" => $offer->__get("package_name"),
            "package_price" => $offer->__get("package_price"),
            "package_icon" => $offer->__get("package_icon"),
            "no_of_clothes" => $offer->__get("no_of_clothes"),
            "saving_percent" => $offer->__get("saving_percent"),
            "validity" => $offer->__get("validity"),
            "no_of_pickups" => $offer->__get("no_of_pickups")
        );
        $where = array(
            "package_id = ?" => $offer->__get("package_id")
        );
        $result = $this->_db_table->update($data, $where);
        return $result;
    }

    public function deletePackageById($id) {
        $where = array(
            "package_id = ?" => $id
        );
        $result = $this->_db_table->delete($where);
        return $result;
    }

}
