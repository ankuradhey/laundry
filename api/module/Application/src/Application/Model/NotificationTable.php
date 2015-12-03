<?php

namespace Assessment\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Sql\Predicate;
use Zend\Db\Sql\Expression;
use Zend\Paginator\Paginator;

class NotificationsTable {

    protected $tableGateway;
    protected $select;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
        $this->select = new Select();
    }

    public function save($data) {
        if (!empty($data['notif_id'])) {
            $select = $this->tableGateway->insert($data);
            return $select;
        } else {
            $id = $data['notif_id'];
            unset($data['notif_id']);
            $result = $this->tableGateway->update($data, array('notif_id' => (int)$id));
            return $result;
        }
    }

    public function delete($notif_id) {
        if(!empty($notif_id)){
            $delete = null;
            $delete = $this->tableGateway->delete(array('notif_id'=>(int)$notif_id));
        }
        return $delete;
    }

    public function fetch($notif_id = ""){
        if(!empty($notif_id)){
            $this->tableGateway->select(array('notif_id'=>(int)$notif_id));
        }else{
            $this->tableGateway->select();
        }
    }
}