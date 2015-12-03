<?php


namespace Application\V1\Rest\Package;

use Zend\Db\Sql\Select;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter;

class PackageMapper {

    protected $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function fetchAll() {
        $select = new Select('packages');
        $paginatorAdapter = new DbSelect($select, $this->adapter);
        $collection = new PackageCollection($paginatorAdapter);
        return $collection;
        
    }

}