<?php

namespace Application\V1\Rest\Category;

use Zend\Db\Sql\Select;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter;

class CategoryMapper {

    protected $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }
    
    public function fetchAll(){
        $select = new Select('categories');
        $select->where(" is_live = '1' ");
        $paginatorAdapter = new DbSelect($select, $this->adapter);
        $collection = new CategoryCollection($paginatorAdapter);
        return $collection;
    }
}