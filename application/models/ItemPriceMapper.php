<?php 

class Application_Model_ItemPriceMapper {

    protected $_db_table;

    public function __construct() {
        $this->_db_table = new Application_Model_DbTable_ItemPrice();
    }

    public function addNewItemPrice(Application_Model_ItemPrice $itemPrice) {
        $data = array(
            'item_id' => $itemPrice->__get("item_id"),
            'service_id' => $itemPrice->__get("service_id"),
            'delivery_type_name' => $itemPrice->__get("delivery_type_name"),
            'price' => $itemPrice->__get("price"),
        );
        $result = $this->_db_table->insert($data);
        if (count($result) == 0) {
            return false;
        } else {
            return $result;
        }
    }

    public function getItemPriceById($item_price_id) {
        $result = $this->_db_table->find($item_price_id);
        if (count($result) == 0) {
            return false;
        }
        $row = $result->current();
        $itemPrice = new Application_Model_ItemPrice($row);
        return $itemPrice;
    }

    public function getAllItemPrice() {
        $result = $this->_db_table->fetchAll(null, array('item_price_id ASC'));
        if (count($result) == 0) {
            return false;
        }
        $itemPrice_object_arr = array();
        foreach ($result as $row) {
            $itemPrice_object = new Application_Model_ItemPrice($row);
            array_push($itemPrice_object_arr, $itemPrice_object);
        }
        return $itemPrice_object_arr;
    }

    public function updateItemPrice(Application_Model_ItemPrice $itemPrice) {
        $data = array(
            'item_id' => $itemPrice->__get("item_id"),
            'service_id' => $itemPrice->__get("service_id"),
            'delivery_type_name' => $itemPrice->__get("delivery_type_name"),
            'price' => $itemPrice->__get("price"),
        );
        $where = "item_price_id = " . $itemPrice->__get("item_price_id");
        $result = $this->_db_table->update($data, $where);
        if (count($result) == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function getItemPriceByServiceIdCatIdDelName($service_id, $delivery_name, $category_id) {
        //echo $franchise_id;exit;
        $query = "SELECT * FROM `item_price`
        INNER JOIN `items` on items.item_id = item_price.item_id WHERE 
        item_price.service_id= '".$service_id."' AND 
        item_price.delivery_type_name= '".$delivery_name."' AND 
        items.category_id='".$category_id."'";
//        $query = "SELECT * FROM `item_price`
//        INNER JOIN `items` on items.item_id = item_price.item_id WHERE item_price.service_id= '".$service_id."' AND item_price.delivery_type_name= '".$delivery_name."' AND items.category_id='".$category_id."'";
//        echo $query;exit;
        $stmt = $this->_db_table->getAdapter()->query($query);
        $result = $stmt->fetchAll();
        //print_r($result);exit;
        if (count($result) == 0) {
            return false;
        }
        $price_arr_obj = array();
        foreach ($result as $row) {
            $prices = new Application_Model_ItemPrice();
            foreach ($row as $key => $value) {
                $prices->__set($key, $value);
            }
            $price_arr_obj[] = $prices;
        }
        return $price_arr_obj;
    }
    public function getItemPriceByItemId($item_id) {
        $where = array(
            "item_id = ?" => $item_id
        );
        $result = $this->_db_table->fetchRow($where);
        if (!$result) {

            return FALSE;
        }
        $item_price = new Application_Model_ItemPrice($result);
        return $item_price;
    }
    public function deleteItemPriceById($item_price_id) {
        $where = "item_price_id = " . $item_price_id;
        $result = $this->_db_table->delete($where);
        if (count($result) == 0) {
            return false;
        } else {
            return true;
        }
    }

}
