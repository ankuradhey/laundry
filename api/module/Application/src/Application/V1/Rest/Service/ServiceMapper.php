<?php

namespace Application\V1\Rest\Service;

use Zend\Db\Sql\Select;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter;

class ServiceMapper {

    public $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function fetchAll($serviceId) {
        $select = new Select('services_master');
        $select->where(" service_status = '1' ");
        $paginatorAdapter = new DbSelect($select, $this->adapter);
        $collection = new ServiceCollection($paginatorAdapter);
        return $collection;
    }
    
    public function fetchByLocation($cityId){
        $select = new Select('services_master');
        $select->where(" find_in_set($cityId,service_city) ");
        $paginatorAdapter = new DbSelect($select, $this->adapter);
        $collection = new ServiceCollection($paginatorAdapter);
        return $collection;
    }

}