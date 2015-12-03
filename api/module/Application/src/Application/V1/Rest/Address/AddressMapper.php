<?php

namespace Application\V1\Rest\Address;

use Zend\Db\Sql\Select;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter;

class AddressMapper {

    protected $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function insert($data) {
        $adapter = $this->adapter;
        $sql = new Sql($this->adapter);
        $insert = $sql->insert('user_address');
        $insert->values($data);
        $selectString = $sql->getSqlStringForSqlObject($insert);
        return $results = $this->adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    }
    
    public function delete($id){
        $adapter = $this->adapter;
        $sql = new Sql($this->adapter);
        $delete = $sql->delete('user_address');
        $delete->from('user_address');
        $delete->where(array('addr_id'=>$id));
        
        $statement = $sql->prepareStatementForSqlObject($delete);
        try {
            $affectedRows = $statement->execute()->getAffectedRows();
        } catch (\Exception $e) {
            die('Error: ' . $e->getMessage());
        }

        return true;
    }

    public function update($data, $addressId) {
        $adapter = $this->adapter;
        $sql = new Sql($adapter);
        $update = $sql->update();
        $update->table('user_address');
        $update->set($data);
        $update->where(array('addr_id' => $addressId));
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

    public function fetchAll($userId) {
        $select = new Select('user_address');
        $select->where(" addr_user_id = '$userId' ");
        $paginatorAdapter = new DbSelect($select, $this->adapter);
        $collection = new AddressCollection($paginatorAdapter);
        return $collection;
    }

    
    public function checkUser($userId) {
        $sql = 'SELECT * FROM users WHERE user_id = ?';
        $resultset = $this->adapter->query($sql, array($userId));
        $data = $resultset->toArray();
        if (!$data) {
            return false;
        }
        return true;
    }

}