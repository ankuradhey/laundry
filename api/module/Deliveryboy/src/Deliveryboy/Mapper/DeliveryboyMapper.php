<?php

namespace Deliveryboy\Mapper;

use Zend\Db\Sql\Select;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter;
use Deliveryboy\Entity\DeliveryboyEntity as DeliveryboyEntity;
use Application\V1\Rest\Order\OrderEntity as OrderEntity;
use Application\V1\Rest\Order\OrderCollection as OrderCollection;
class DeliveryboyMapper {

    protected $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }
    
    public function insert($data){
        $adapter = $this->adapter;
        $sql = new Sql($this->adapter);
        $insert = $sql->insert('order_products');
        $insert->values($data);
        $selectString = $sql->getSqlStringForSqlObject($insert);
        return $results = $this->adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    }
    
    public function authenticate($userName, $password){
        $sql = 'SELECT * FROM delivery_boy WHERE delboy_username = ? and delboy_password = ? ';
        $resultset = $this->adapter->query($sql, array($userName, $password));
        $data = $resultset->toArray();
        if (!$data) {
            return false;
        }

        $entity = new DeliveryboyEntity();
        $entity->exchangeArray($data[0]);
        return $entity;
    }
    
    public function fetchOrders($id, $orderStatus = NULL){
        $select = new Select('orders');
        $select->where(" order_delivery_boy = '$id' ");
        if($orderStatus){
            if($orderStatus == 'pickup')
                $select->where(" order_status is NULL  ");
            elseif($orderStatus == 'delivery')
                $select->where(" order_status = 'picked'  ");
        }
        $paginatorAdapter = new DbSelect($select, $this->adapter);
        $collection = new OrderCollection($paginatorAdapter);
        return $collection;
    }
    
    public function checkOrder($orderId){
        $sql = 'SELECT * FROM orders WHERE order_id = ? ';
        $resultset = $this->adapter->query($sql, array($orderId));
        $data = $resultset->toArray();
        if (!$data) {
            return false;
        }

        $entity = new OrderEntity();
        $entity->exchangeArray($data[0]);
        return $entity;
    }
    
    public function checkDeliveryBoy($id){
        $sql = 'SELECT * FROM delivery_boy WHERE delboy_id = ? ';
        $resultset = $this->adapter->query($sql, array($id));
        $data = $resultset->toArray();
        if (!$data) {
            return false;
        }

        $entity = new DeliveryboyEntity();
        $entity->exchangeArray($data[0]);
        return $entity;
    }
    
    public function fetchOrderProducts($orderId){
        $select = new Select('order_products');
        $select->where(" order_id = '$orderId' ");
        $paginatorAdapter = new DbSelect($select, $this->adapter);
        $collection = new OrderCollection($paginatorAdapter);
        return $collection;
    }
}