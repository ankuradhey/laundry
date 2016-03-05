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
        $select->order('order_id desc');
        $paginatorAdapter = new DbSelect($select, $this->adapter);
        $collection = new OrderCollection($paginatorAdapter);
        return $collection;
    }

    public function fetchOne($userId) {
        //fetch packages
        $packages = $this->fetchPackages();
        $services = $this->fetchServices();

        $sql = 'SELECT * FROM orders WHERE order_user_id = ? order by order_id desc';
        $result = $this->adapter->query($sql, array($userId));
        $result = $result->toArray();
        $resultArr = array();
        foreach($result as $resultKey=>$resultVal){
            if($resultVal['order_type'] == 'service'){
                $serviceIds = explode(',',$resultVal['order_service_type']);
                if(!empty($serviceIds) && count($serviceIds)){
                    $serviceIds = array_map(function($val) use ($services){
                        if(isset($services[$val]))
                            return  $services[$val];
                    }, $serviceIds);

                    $result[$resultKey]['order_service_type'] = implode(",",$serviceIds);
                }
            }
            elseif($resultVal['order_type'] == 'package'){
                $packageIds = explode(',',$resultVal['order_service_type']);
                if(!empty($packageIds) && count($packageIds)){
                    $packageIds = array_map(function($val) use ($packages){
                       return  $packages[$val];
                    }, $packageIds);

                    $result[$resultKey]['order_service_type'] = implode(",",$packageIds);
                }
            }
        }
//        $resultSet = new \Zend\Db\ResultSet\ResultSet();
//        $resultSet->initialize($result);
//        $paginatorAdapter = new DbSelect($select, $this->adapter);
        $paginatorAdapter = new \Zend\Paginator\Adapter\ArrayAdapter($result);
//        $paginatorAdapter = new \Zend\Paginator\Adapter\ArrayAdapter($result);
        $collection = new OrderCollection($paginatorAdapter);
        return $collection;
    }
    
    public function fetchByOrder($orderId) {
        $sql = 'SELECT * FROM orders WHERE order_id = ?';
        $resultset = $this->adapter->query($sql, array($orderId));
//        $select = new Select('orders');
//        $select->where(" order_id = '$orderId' ");
        
        $data = $resultset->toArray();
        if (!$data) {
            return false;
        }
        
//        $paginatorAdapter = new DbSelect($select, $this->adapter);
//        $collection = new OrderCollection($paginatorAdapter);
//        return $collection;

//        $entity = new OrderEntity();
//        $entity->exchangeArray($data[0]);
        return $data[0];
    }

    public function checkUser($userId) {
        $sql = 'SELECT * FROM users WHERE user_id = ?';
        $resultset = $this->adapter->query($sql, array($userId));
        $data = $resultset->toArray();
        if (!$data) {
            return false;
        }
        return $data;
    }
    
    public function fetchServices(){
        $sql = 'SELECT * FROM services_master WHERE service_status = ?';
        $resultset = $this->adapter->query($sql, array('1'));
        $data = $resultset->toArray();
        $servicesArr = array();
        foreach($data as $val){
            $servicesArr[$val['service_id']] = $val['service_name'];
        }
        return $servicesArr;
    }
    
    public function fetchPackages(){
        $sql = 'SELECT * FROM packages where ?';
        $resultset = $this->adapter->query($sql, array('1'));
        $data = $resultset->toArray();
        $packagesArr = array();
        foreach($data as $val){
            $packagesArr[$val['package_id']] = $val['package_name'];
        }
        return $packagesArr;
    }

}