<?php
namespace Usertrack\V1\Rest\Package;

use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

class PackageResource extends AbstractResourceListener
{
    protected $mapper;
    
    public function __construct($mapper){
        $this->mapper = $mapper;
    }
    /**
     * Create a resource
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        //check user validity
        $data = (array)$data;
        $userId = $data['usertrack_user_id'];
        $res = $this->mapper->checkUser($userId);
        
        if(!$res)
            return new ApiProblem(405, 'User not found');
        else{
            //check packages
            $res = $this->mapper->checkPackage($data['usertrack_package_id']);
            if(!$res){
                return new ApiProblem(405, 'Package not found');
            }else{
                $data['usertrack_order_id'] = $data['order_id'];
                $data['clothes_left'] = $data['clothes_availed'] =$res->no_of_clothes;
                $data['pickups_left'] = $data['pickups_availed'] = $res->no_of_pickups;
                $data['usertrack_start_date'] = date('Y-m-d');
                $data['usertrack_expiry_date'] = date('Y-m-d',strtotime("+$res->validity Month ".date('Y-m-d') ));
                unset($data['order_id']);
                return $this->mapper->insert($data);
            }
            
        }
            
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
        //validate user id
        $res = $this->mapper->checkUser($id);
        if(!$res)
            return new ApiProblem(405, 'No user found');
        
        return $this->mapper->fetchAll($id);
        return new ApiProblem(405, 'The GET method has not been defined for individual resources');
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = array())
    {
        return new ApiProblem(405, 'The GET method has not been defined for collections');
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
        return new ApiProblem(405, 'The PUT method has not been defined for individual resources');
    }
}
