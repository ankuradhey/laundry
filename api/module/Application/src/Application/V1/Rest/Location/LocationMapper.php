<?php

namespace Application\V1\Rest\Location;

use Zend\Db\Sql\Select;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter;

class LocationMapper {

    protected $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }
    
    public function fetchAll(){
        $select = new Select('city');
        $select->join("local_area","city.city_id = local_area.local_area_city",array('local_area_id', 'local_area_name'));
        $select->where(" city_status = '1' and local_area_status = '1' ");
        $paginatorAdapter = new DbSelect($select, $this->adapter);
        $collection = new LocationCollection($paginatorAdapter);
        return $collection;
    }
    
    public function fetchAllCities(){
        $select = new Select('city');
        $select->where(" city_status = '1' ");
        $paginatorAdapter = new DbSelect($select, $this->adapter);
        $collection = new LocationCollection($paginatorAdapter);
        return $collection;
    }
}