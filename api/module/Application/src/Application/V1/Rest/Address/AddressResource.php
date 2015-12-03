<?php
namespace Application\V1\Rest\Address;

use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

class AddressResource extends AbstractResourceListener
{
    public $mapper;
    
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
        //check user_id
        $data = (array)$data;
        $userId = $data['addr_user_id'];
        $res = $this->mapper->checkUser((int)$userId);
        if(!$res)
            return new ApiProblem(405, 'User with id '.$userId.' not found');
        
        return $this->mapper->insert($data);
    }

    /**
     * Delete a resource
     *
     * @param  mixed $id
     * @return ApiProblem|mixed
     */
    public function delete($id)
    {
        return $this->mapper->delete($id);
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
        //check user id
        $res = $this->mapper->checkUser((int)$id);
        if(!$res)
            return new ApiProblem(405, 'User with id '.$id.' not found');
        
        return $this->mapper->fetchAll((int)$id);
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
     * @param  mixed $id - address id
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function update($id, $data)
    {
        try{
            return $this->mapper->update((array)$data,$id);
        }
        catch(\Exception $e){
            return new ApiProblem(405, $e);
        }
        
    }
}
