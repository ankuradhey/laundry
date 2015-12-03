<?php

namespace Application\V1\Rest\Notification;

use Zend\Db\Sql\Select;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter;

class NotificationMapper {

    protected $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function insert($data) {
        $adapter = $this->adapter;
        $sql = new Sql($this->adapter);
        $insert = $sql->insert('notification');
        $insert->values($data);
        $selectString = $sql->getSqlStringForSqlObject($insert);
        return $results = $this->adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    }

    public function update($data, $notifciationId) {
        $adapter = $this->adapter;
        $sql = new Sql($adapter);
        $update = $sql->update();
        $update->table('notification');
        $update->set($data);
        $update->where(array('notif_id' => $notifciationId));
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

    public function fetchAll($userId) {
        $select = new Select('notification');
        $select->where(" notif_user_id = '$userId' ");
        $paginatorAdapter = new DbSelect($select, $this->adapter);
        $collection = new NotificationCollection($paginatorAdapter);
        return $collection;
    }

    public function fetchOne($userId) {
        $sql = 'SELECT * FROM notification WHERE notif_user_id = ?';
        $resultset = $this->adapter->query($sql, array($userId));
        $data = $resultset->toArray();
        if (!$data) {
            return false;
        }

        $entity = new NotificationEntity();
        $entity->exchangeArray($data[0]);
        return $entity;
    }

    public function checkPriceKeys($serviceId, $cityId, $categoryId) {
        $sql = 'SELECT * FROM item_price WHERE service_id = ? and item_city_id = ';
        $resultset = $this->adapter->query($sql, array($userId));
        $data = $resultset->toArray();
        if (!$data) {
            return false;
        }
        return true;
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