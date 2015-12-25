<?php

namespace Deliveryboy\V1\Rest\Orderproducts;

use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

class OrderproductsResource extends AbstractResourceListener {

    protected $mapper;

    public function __construct($mapper) {
        $this->mapper = $mapper;
    }

    /**
     * Create a resource
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function create($data) {
        //find order id is valid or not
        $data = (array) $data;
//        $res = $this->mapper->checkOrder($data['order_id']);
//        if(!$res)
//            return new ApiProblem(405, 'Order id not found');
        $orderId = false;
        $flagPackage = false;
        $serviceList = null;
        $orderStatus = 'picked';
        $totalAmount = 0;
        if (isset($data) && count($data)) {
            $orderId = $data[0]['order_id'];
            //get services list
            $orderDetails = $this->mapper->getOrderMapper()->fetchByOrder($val['order_id']);
            if ($orderDetails['order_type'] == 'service') {
                $serviceList = $orderDetails['order_service_type'];
                $serviceList = explode(',', $serviceList);
                //if no service found - order is not proper
                if (count($serviceList) == 0 || empty($serviceList)) {
                    return new ApiProblem(405, 'Incorrect order. No service selected.');
                }
            }

            foreach ($data as $val) {
                //check for valid orders
                $res = $this->mapper->checkOrder($val['order_id']);

                if (!$res)
                    return new ApiProblem(405, 'Order id not found');

                //===check if new service is there then add in order table
                //--- 1. get services list (to check from the currently adding product + services list)
                $serviceList;
                //--- 2. Check if service is new - if found then update order_products table
                if (!in_array($val['order_service'], $serviceList) && isset($val['order_service_name'])) {
                    $orderUpdateArr = array();
                    $serviceList[] = $val['order_service_name'];
                    $orderUpdateArr['order_service_type'] = implode(",", $serviceList);
                    $orderUpdateArr['order_status'] = $val['order_status'];
                    $ret = $this->orderMapper->update($orderUpdateArr, $orderId);
                }

                if (!isset($val['order_product_id']))
                    $this->mapper->insert($val);
                else {
                    $orderProductId = $val['order_product_id'];
                    unset($val['order_product_id']);
                    $this->mapper->update($val, $orderProductId);
                }

                if ($val['order_type'] == 'package') {
                    $flagPackage = true;
                }

                if ($val['order_status'] == 'returned') {
                    $orderStatus = 'returned';
                } elseif ($val['order_status'] != 'delivered' && $orderStatus != 'returned') {
                    $orderStatus = null;
                } elseif (isset($val['order_status']) && isset($orderStatus) && $orderStatus != 'returned') {
                    if ($val['order_status'] == 'returned') {
                        $orderStatus = 'returned';
                    } else {
                        $orderStatus = $val['order_status'];
                    }
                }
                $totalAmount += $val['total_price'];
            }
        } else {
            return new ApiProblem(405, 'Order products not found');
        }

        if (!$orderId)
            return new ApiProblem(405, 'Order id not found');

        if ($flagPackage)
            $this->mapper->updatePackage($orderId, null, 1);

        $updateOrderData = array();
        $updateOrderData['order_status'] = $orderStatus;
        if ($orderStatus == 'picked') {
            $serviceTax = $totalAmount * 14 / 100;
            $totalAmount += $totalAmount * 14 / 100;
            $updateOrderData['service_tax'] = $totalAmount;
            if($totalAmount < 200){
                $totalAmount += 50;
                $updateOrderData['delivery_charge'] = 50;
                $updateOrderData['order_amount'] = $totalAmount;
            }
        }
        $this->mapper->updateOrder($orderId, $updateOrderData);
        return true;
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
        return $this->mapper->fetchOrderProducts($id);
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = array()) {
        return new ApiProblem(405, 'The GET method has not been defined for collections');
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
