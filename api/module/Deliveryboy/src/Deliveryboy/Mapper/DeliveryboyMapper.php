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
use Zend\ServiceManager\ServiceManager as ServiceManager;

class DeliveryboyMapper {

    protected $adapter, $serviceManger, $orderMapper;

    public function __construct(AdapterInterface $adapter, ServiceManager $sm) {
        $this->adapter = $adapter;
        $this->serviceManger = $sm;
    }

    public function getOrderMapper() {
        if (!isset($orderMapper)) {
            $this->orderMapper = $this->serviceManager->get('Application\V1\Rest\Order\OrderMapper');
        }
        return $this->orderMapper;
    }

    public function updateOrder($orderId, $data) {
        if (isset($data['order_status'])) {
            if ($data['order_status'] == 'returned') {
                $sql = "update orders set order_delivery = DATE_ADD(order_delivery, interval 2 day) WHERE order_id = ? ";
            } else {
                $sql = "update orders set order_status = '".$data['order_status']."' WHERE order_id = ? ";
            }
            
            $resultset = $this->adapter->query($sql, array($orderId));
            return $resultset;
        } else {
            return false;
        }
    }

    public function updatePackage($orderId, $clothesLeft = null, $pickupsLeft = null) {
        $setSql = ' set ';
        if ($clothesLeft) {
            $setSql = ' clothes_left = clothes_left - ' . (int) $clothesLeft;
        }

        if ($pickupsLeft) {
            $setSql = ' pickups_left = pickups_left - ' . (int) $pickupsLeft;
        }

        $sql = "update user_track ut
                    $set
                    join order o on o.order_service_type = ut.usertrack_package_id and  o.order_user_id = ut.usertrack_user_id and order_delivery_status is NULL and usertrack_expiry_date >= now()  
                    WHERE o.order_id = ?
                    limit 1 ";
        $resultset = $this->adapter->query($sql, array($orderId));
    }

    public function insert($data) {
        $adapter = $this->adapter;
        $sql = new Sql($this->adapter);
        $insert = $sql->insert('order_products');
        $insert->values($data);
        $selectString = $sql->getSqlStringForSqlObject($insert);
        $results = $this->adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);

        //if type package is there then reduce number of clothes and days
        if ($results && $data['order_type'] == 'package') {
            $this->updatePackage($data['order_id'], $data['quantity']);
        }
    }

    //updating order_product relation
    public function update($data, $orderProductId) {
        $adapter = $this->adapter;
        $sql = new Sql($adapter);
        $update = $sql->update();
        $update->table('order_products');
        $update->set($data);
        $update->where(array('order_product_id' => $orderProductId));
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

    public function authenticate($userName, $password) {
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

    public function fetchOrders($id, $orderStatus = NULL) {
        $select = new Select('orders');

        $select->join(
                array('ut' => 'user_track'), ' ut.usertrack_package_id = orders.order_service_type ', array('clothes_left', 'pickups_left'), 'left'
        );


        if ($orderStatus) {
            if ($orderStatus == 'pickup') {
                $select->where(" order_pickup_boy = '$id' and order_status is NULL ");
//                $select->where(" order_status is NULL  ");
            } elseif ($orderStatus == 'delivery') {
                $select->where(" order_delivery_boy = '$id' and order_status is 'picked' ");
//                $select->where(" order_status = 'picked'  ");
            }
        }
        $paginatorAdapter = new DbSelect($select, $this->adapter);
        $collection = new OrderCollection($paginatorAdapter);
        return $collection;
    }

    public function checkOrder($orderId) {
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

    public function checkDeliveryBoy($id) {
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

    public function fetchOrderProducts($orderId) {
        $select = new Select('order_products');
        $select->where(" order_id = '$orderId' ");
        $paginatorAdapter = new DbSelect($select, $this->adapter);
        $collection = new OrderCollection($paginatorAdapter);
        return $collection;
    }

}