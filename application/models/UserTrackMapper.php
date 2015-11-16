<?php

class Application_Model_UserTrackMapper {

    protected $_db_table;

    public function __construct() {
        $this->_db_table = new Application_Model_DbTable_UserTrack();
    }

    public function addNewTrack(Application_Model_UserTrack $userTrack) {
        $data = array(
            "usertrack_user_id" => $userTrack->__get("usertrack_user_id"),
            "track_type" => $userTrack->__get("track_type"),
            "usertrack_package_id" => $userTrack->__get("usertrack_package_id"),
            "usertrack_offer_id" => $userTrack->__get("usertrack_offer_id"),
            "clothes_left" => $userTrack->__get("clothes_left"),
            "clothes_availed" => $userTrack->__get("clothes_availed"),
            "pickups_left" => $userTrack->__get("pickups_left"),
            "pickups_availed" => $userTrack->__get("pickups_availed"),
            "usertrack_start_date" => $userTrack->__get("usertrack_start_date"),
            "usertrack_expiry_date" => $userTrack->__get("usertrack_expiry_date"),
            "usertrack_house" => $userTrack->__get("usertrack_house"),
            "usertrack_locality" => $userTrack->__get("usertrack_locality"),
            "usertrack_city" => $userTrack->__get("usertrack_city"),
        );

        $result = $this->_db_table->insert($data);
        return($result);
    }
    
    public function getUserPackageById($id){
        $where = array(
            "usertrack_id = ?" => (int)$id
        );
        $result = $this->_db_table->fetchRow($where);
        if (!$result) {

            return FALSE;
        }
        $package = new Application_Model_UserTrack($result);
        return $package;
    }
    
    public function getUserPackages($userId,$expired=false) {
        
        $query = "select user_track.*, package_name from user_track join packages on packages.package_id =  user_track.usertrack_package_id ";
        $where = " where 1 ";
        if($expired)
        {
            $where .= " and usertrack_expiry_date > curdate() ";
        }
        
        $where .= " and usertrack_user_id = $userId ";
        $stmt = $this->_db_table->getAdapter()->query($query.$where);
        $result = $stmt->fetchAll();
        //print_r($result);exit;
        if (count($result) == 0) {
            return false;
        }
        $userTrackArr = array();
        foreach ($result as $row) {
            $userTrack = new Application_Model_UserTrack();
            foreach ($row as $key => $value) {
                $userTrack->__set($key, $value);
            }
            $userTrackArr[] = $userTrack;
        }
        return $userTrackArr;
    }
    
}