<?php

namespace Application\V1\Rest\Page;

use Zend\Db\Sql\Select;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter;

class PageMapper {

    protected $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function fetchOne($pageId) {
        $sql = 'SELECT * FROM pages WHERE page_id = ?';
        $resultset = $this->adapter->query($sql, array($pageId));
        $data = $resultset->toArray();
        if (!$data) {
            return false;
        }

        $entity = new PageEntity();
        $entity->exchangeArray($data[0]);
        return $entity;
    }

}