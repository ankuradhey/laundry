<?php


namespace Application\V1\Rest\Order;

use Zend\Db\Sql\Select;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter;

class OrderMapper {

    protected $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function insert($data) {
        $adapter = $this->adapter;
        $sql = new Sql($this->adapter);
        $insert = $sql->insert('orders');
        $insert->values($data);
        $selectString = $sql->getSqlStringForSqlObject($insert);
        $results = $this->adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return array("order_id"=>$results->getGeneratedValue());
    }

    public function updatePackage(){
        
    }
    
    public function update($data, $orderId) {
        $adapter = $this->adapter;
        $sql = new Sql($adapter);
        $update = $sql->update();
        $update->table('orders');
        $update->set($data);
        $update->where(array('order_id' => $orderId));
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

    public function fetchAll() {
        $select = new Select('orders');
        $paginatorAdapter = new DbSelect($select, $this->adapter);
        $collection = new OrderCollection($paginatorAdapter);
        return $collection;
    }

    public function fetchOne($userId) {
        $select = new Select('orders');
        $select->where(' order_user_id = "'.$userId.'" ');
        $paginatorAdapter = new DbSelect($select, $this->adapter);
        $collection = new OrderCollection($paginatorAdapter);
        return $collection;
        
    }
    
    public function fetchByOrder($orderId) {
        $sql = 'SELECT * FROM orders WHERE order_id = ?';
        $resultset = $this->adapter->query($sql, array($orderId));
        $data = $resultset->toArray();
        if (!$data) {
            return false;
        }

        $entity = new OrderEntity();
        $entity->exchangeArray($data[0]);
        return $entity;
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

}