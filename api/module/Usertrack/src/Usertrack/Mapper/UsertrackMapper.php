<?php

namespace Usertrack\Mapper;

use Zend\Db\Sql\Select;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter;
use Usertrack\Entity\UsertrackEntity as UsertrackEntity;
use Application\V1\Rest\Package\PackageEntity as PackageEntity;
use Application\V1\Rest\Offer\OfferEntity as OfferEntity;
use Usertrack\V1\Rest\Package\PackageCollection as PackageCollection;
use Application\V1\Rest\Offer\OfferCollection as OfferCollection;
class UsertrackMapper {

    protected $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function insert($data) {
        $adapter = $this->adapter;
        $sql = new Sql($this->adapter);
        $insert = $sql->insert('user_track');
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

    public function fetchAll($userId, $cond = null) {
        $select = new Select('user_track');
        
        if($cond == null){
            $select->join('packages','packages.package_id = user_track.usertrack_package_id',array('package_name'));
            $select->where(" usertrack_user_id = '$userId' and track_type = 'package' ");
        }
        else{
            $select->where(" usertrack_user_id = '$userId' and track_type = 'offer' and offer_status = '1' ");
        }
        
        $select->where(" usertrack_user_id = '$userId' ");
        
        
        $paginatorAdapter = new DbSelect($select, $this->adapter);
        $collection = new PackageCollection($paginatorAdapter);
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
    
    public function checkPackage($packageId){
        $sql = 'SELECT * FROM packages WHERE package_id = ?';
        $resultset = $this->adapter->query($sql, array($packageId));
        $data = $resultset->toArray();
        if (!$data) {
            return false;
        }
        $entity = new PackageEntity();
        $entity->exchangeArray($data[0]);
        return $entity;
    }
    
    public function checkOffer($offerId){
        $sql = 'SELECT * FROM offers WHERE offer_id = ? and offer_status = ?';
        $resultset = $this->adapter->query($sql, array($offerId,'1'));
        $data = $resultset->toArray();
        if (!$data) {
            return false;
        }
        $entity = new OfferEntity();
        $entity->exchangeArray($data[0]);
        return $entity;
    }

}