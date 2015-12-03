<?php


namespace Application\V1\Rest\Offer;

use Zend\Db\Sql\Select;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter;

class OfferMapper {

    protected $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function fetchAll() {
        $select = new Select('offers');
        $select->where(' offer_status = "1" ');
        $paginatorAdapter = new DbSelect($select, $this->adapter);
        $collection = new OfferCollection($paginatorAdapter);
        return $collection;
    }

}