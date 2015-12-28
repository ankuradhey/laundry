<?php
namespace Application\V1\Rest\Order;

use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

class OrderResource extends AbstractResourceListener
{
    public $mapper, $userMapper, $services;
    
    public function __construct($services) {
        $this->services = $services;
        $this->mapper = $services->get('Application\V1\Rest\Order\OrderMapper');
    }
    
    protected function getUserMapper(){
        if(!isset($this->userMapper)){
            $this->userMapper = $this->services->get('Application\V1\Rest\User\UserMapper');
        }
        return $this->userMapper;
    }
    /**
     * Create a resource
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        //check if valid user
        $data = (array)$data;
        //returns user info
        $res = $this->mapper->checkUser((int)$data['order_user_id']);
        
        if(!$res)
            return new ApiProblem(405, 'User with id '.$data['order_user_id'].' not found');
        
        $ret = $this->mapper->insert($data);
        if(!isset($res[0]['user_email'])){
            $userMapper = $this->getUserMapper();
            $userMapper->update(array('user_email'=>$data['order_user_email']),$data['order_user_id']);
        }
        return $ret;
    }

    /**
     * Delete a resource
     *
     * @param  mixed $id
     * @return ApiProblem|mixed
     */
    public function delete($id)
    {
        return new ApiProblem(405, 'The DELETE method has not been defined for individual resources');
    }

    /**
     * Delete a collection, or members of a collection
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function deleteList($data)
    {
        return new ApiProblem(405, 'The DELETE method has not been defined for collections');
    }

    /**
     * Fetch a resource
     *
     * @param  mixed $id
     * @return ApiProblem|mixed
     */
    public function fetch($id)
    {
        return $this->mapper->fetchByOrder($id);
        //return new ApiProblem(405, 'The GET method has not been defined for individual resources');
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = array())
    {
        $userId =  $params->get('user_id');
        return $this->mapper->fetchOne($userId);
        
    }

    /**
     * Patch (partial in-place update) a resource
     *
     * @param  mixed $id
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function patch($id, $data)
    {
        return new ApiProblem(405, 'The PATCH method has not been defined for individual resources');
    }

    /**
     * Replace a collection or members of a collection
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function replaceList($data)
    {
        return new ApiProblem(405, 'The PUT method has not been defined for collections');
    }

    /**
     * Update a resource
     *
     * @param  mixed $id
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function update($id, $data)
    {
        return $this->mapper->update((array)$data, (int)$id);
    }
}
