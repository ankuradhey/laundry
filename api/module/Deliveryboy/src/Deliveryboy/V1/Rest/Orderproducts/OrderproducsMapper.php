<?php

namespace Deliveryboy\Mapper;

use Zend\Db\Sql\Select;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter;
use Deliveryboy\Entity\DeliveryboyEntity as DeliveryboyEntity;
use Application\V1\Rest\Order\OrderCollection as OrderCollection;
class DeliveryboyMapper {

    protected $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

}