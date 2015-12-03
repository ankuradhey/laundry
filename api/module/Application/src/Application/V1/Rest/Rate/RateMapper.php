<?php

namespace Application\V1\Rest\Rate;

use Zend\Db\Sql\Select;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter;

class RateMapper {

    protected $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function insert($data) {
        $adapter = $this->adapter;
        $sql = new Sql($this->adapter);
        $insert = $sql->insert('item_price');
        $insert->values($data);
        $selectString = $sql->getSqlStringForSqlObject($insert);
        return $results = $this->adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    }

    public function update($data, $notifciationId) {
        $adapter = $this->adapter;
        $sql = new Sql($adapter);
        $update = $sql->update();
        $update->table('item_price');
        $update->set($data);
        $update->where(array('item_price_id' => $notifciationId));
        $statement = $sql->prepareStatementForSqlObject($update);
        try {
            $affectedRows = $statement->execute()->getAffectedRows();
        } catch (\Exception $e) {
            die('Error: ' . $e->getMessage());
        }
        if (empty($affectedRows)) {
            die('Zero rows affected');
        }

        return $affectedRows;
    }

    public function fetchAll($cityId, $serviceId, $categoryId, $deliveryType = 'Regular') {
        $select = new Select('item_price');
        $select->columns(array("item_price_id", "item_id","service_id","item_city_id","delivery_type_name","price"));
        $select->join("items","items.item_id = item_price.item_id",array("item_name"));
        $select->where(" item_city_id = '$cityId' and item_price.service_id = '$serviceId' and items.category_id = '$categoryId' and delivery_type_name = '$deliveryType' ");
//        echo '<pre>'; print_r($select->getSqlString()); exit('Macro die');
        $paginatorAdapter = new DbSelect($select, $this->adapter);
        $collection = new RateCollection($paginatorAdapter);
        return $collection;
    }

    public function checkUser($userId) {
        $sql = 'SELECT * FROM users WHERE user_id = ?';
        $resultset = $this->adapter->query($sql, array($userId));
        $data = $resultset->toArray();
        if (!$data) {
            return false;
        }
        return true;
    }
    
    public function checkService($serviceId){
        $sql = 'SELECT * FROM services_master WHERE service_id = ?';
        $resultset = $this->adapter->query($sql, array($serviceId));
        $data = $resultset->toArray();
        if (!$data) {
            return false;
        }
        return true;
    }
    
    public function checkCity($cityId){
        $sql = 'SELECT * FROM city WHERE city_id = ?';
        $resultset = $this->adapter->query($sql, array($cityId));
        $data = $resultset->toArray();
        if (!$data) {
            return false;
        }
        return true;
    }
    
    public function checkCategory($categoryId){
        $sql = 'SELECT * FROM categories WHERE category_id = ?';
        $resultset = $this->adapter->query($sql, array($categoryId));
        $data = $resultset->toArray();
        if (!$data) {
            return false;
        }
        return true;
    }

}