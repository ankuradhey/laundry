<?php 

class Application_Model_ItemsMapper {

    protected $_db_table;

    public function __construct() {
        $this->_db_table = new Application_Model_DbTable_Items();
    }

    public function addNewItem(Application_Model_Items $item) {
        $data = array(
            "item_name" => $item->__get("item_name"),
            "item_order" => $item->__get("item_order"),
            "is_live" => $item->__get("is_live"),
            "item_description" => $item->__get("item_description"),
            "category_id" => $item->__get("category_id"),
            "service_id" => $item->__get("service_id"),
            "item_image" => $item->__get("item_image")
        );

        $result = $this->_db_table->insert($data);
        return($result);
    }

    public function getAllItems($is_live = false) {
        if ($is_live) {
            $where = array("is_live = ?" => 1);
        } else {
            $where = null;
        }
        $result = $this->_db_table->fetchAll($where, array('item_order ASC'));
        if (count($result) == 0) {
            return false;
        }
        $items_arr = array();
        foreach ($result as $row) {
            $item = new Application_Model_Items($row);
            $items_arr[] = $item;
        }
        return $items_arr;
    }

    public function getItemById($id) {
        $where = array(
            "item_id = ?" => $id
        );
        $result = $this->_db_table->fetchRow($where);
        if (!$result) {

            return FALSE;
        }
        $item = new Application_Model_Items($result);
        return $item;
    }

    public function getItemsByServiceIdAndItemId($service_id, $items){
        $where = array("service_id = $service_id  and item_id in ($items)");
        $result = $this->_db_table->fetchAll($where, array('item_order ASC'));
        if (count($result) == 0) {
            return false;
        }
        $items_arr = array();
        foreach ($result as $row) {
            $item = new Application_Model_Items($row);
            $items_arr[] = $item;
        }
        
        return $items_arr;
        
    }
    
    public function getItemsByCategoryIdServiceId($category_id, $service_id) {

        $where = array("category_id = ?" => $category_id, "service_id = ?" => $service_id,);

        $result = $this->_db_table->fetchAll($where, array('item_order ASC'));
        if (count($result) == 0) {
            return false;
        }
        $items_arr = array();
        foreach ($result as $row) {
            $item = new Application_Model_Items($row);
            $items_arr[] = $item;
        }
        
        return $items_arr;
    }

    public function updateItem(Application_Model_Items $item) {
        $data = array(
            "item_name" => $item->__get("item_name"),
            "item_order" => $item->__get("item_order"),
            "is_live" => $item->__get("is_live"),
            "item_description" => $item->__get("item_description"),
            "category_id" => $item->__get("category_id"),
            "service_id" => $item->__get("service_id"),
            "item_image" => $item->__get("item_image")
        );
        $where = array(
            "item_id = ?" => $item->__get("item_id")
        );
        $result = $this->_db_table->update($data, $where);
        return $result;
    }

    public function deleteItemById($id) {
        $where = array(
            "item_id = ?" => $id
        );
        $result = $this->_db_table->delete($where);
        return $result;
    }

}
