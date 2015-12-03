<?php

namespace Application\V1\Rest\Rate;

use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

class RateResource extends AbstractResourceListener {

    protected $service;
    protected $rateMapper;

    public function __construct($service) {
        $this->service = $service;
        $this->rateMapper = $service->get('Application\V1\Rest\Rate\RateMapper');
    }

    /**
     * Create a resource
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function create($data) {
        //check all list like item like service_id, iem_city_id, item_category_id
        $data = (array) $data;
        $res = $this->rateMapper->checkService($data['service_id']);
        if (!$res) {
            return new ApiProblem(405, 'Service with id ' . $data['service_id'] . ' not found');
        }

        $res = $this->rateMapper->checkCity($data['item_city_id']);
        if (!$res) {
            return new ApiProblem(405, 'City with id ' . $data['item_city_id'] . ' not found');
        }

        $res = $this->rateMapper->checkCategory($data['item_category_id']);
        if (!$res) {
            return new ApiProblem(405, 'Category with id ' . $data['item_category_id'] . ' not found');
        }

        return $this->rateMapper->insert($data);
    }

    /**
     * Delete a resource
     *
     * @param  mixed $id
     * @return ApiProblem|mixed
     */
    public function delete($id) {
        return new ApiProblem(405, 'The DELETE method has not been defined for individual resources');
    }

    /**
     * Delete a collection, or members of a collection
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function deleteList($data) {
        return new ApiProblem(405, 'The DELETE method has not been defined for collections');
    }

    /**
     * Fetch a resource
     *
     * @param  mixed $id
     * @return ApiProblem|mixed
     */
    public function fetch($id) {
        return new ApiProblem(405, 'The GET method has not been defined for individual resources');
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = array()) {
        $e = $this->getEvent();
        $route = $e->getRouteMatch();
        $service_id = $route->getParam('service_id');
        $city_id = $route->getParam('city_id');
        $category_id = $route->getParam('category_id');
        $delivery_type = $route->getParam('delivery_type');
        return $this->rateMapper->fetchAll($city_id, $service_id, $category_id, $delivery_type);
    }

    /**
     * Patch (partial in-place update) a resource
     *
     * @param  mixed $id
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function patch($id, $data) {
        return new ApiProblem(405, 'The PATCH method has not been defined for individual resources');
    }

    /**
     * Replace a collection or members of a collection
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function replaceList($data) {
        return new ApiProblem(405, 'The PUT method has not been defined for collections');
    }

    /**
     * Update a resource
     *
     * @param  mixed $id
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function update($id, $data) {
        return new ApiProblem(405, 'The PUT method has not been defined for individual resources');
    }

}
