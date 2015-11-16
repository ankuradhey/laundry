<?php

class Application_Model_CategoriesMapper {

    protected $_db_table;

    public function __construct() {
        $this->_db_table = new Application_Model_DbTable_Categories();
    }

    public function addNewCategory(Application_Model_Categories $category) {
        $data = array(
            "category_name" => $category->__get("category_name"),
            "category_order" => $category->__get("category_order"),
            "is_live" => $category->__get("is_live"),
        );

        $result = $this->_db_table->insert($data);
        return($result);
    }

    public function getAllCategories($is_live=false) {
        if($is_live)
        {
            $where = array("is_live = ?"=>1);
        }
        else
        {
            $where = null;
        }
        $result = $this->_db_table->fetchAll($where, array('category_order ASC'));
        if (count($result) == 0) {
            return false;
        }
        $categories_arr = array();
        foreach ($result as $row) {
            $category = new Application_Model_Categories($row);
            $categories_arr[] = $category;
        }
        return $categories_arr;
    }

    public function getCategoryById($id) {
        $where = array(
            "category_id = ?" => $id
        );
        $result = $this->_db_table->fetchRow($where);
        if (!$result) {

            return FALSE;
        }
        $category = new Application_Model_Categories($result);
        return $category;
    }

    public function updateCategory(Application_Model_Categories $category) {
        $data = array(
            "category_name" => $category->__get("category_name"),
            "category_order" => $category->__get("category_order"),
            "is_live" => $category->__get("is_live"),
        );
        $where = array(
            "category_id = ?" => $category->__get("category_id")
        );
        $result = $this->_db_table->update($data, $where);
        return $result;
    }

    public function deleteCategoryById($id) {
        $where = array(
            "category_id = ?" => $id
        );
        $result = $this->_db_table->delete($where);
        return $result;
    }

}
