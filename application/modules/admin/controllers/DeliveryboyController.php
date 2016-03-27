<?php

class Admin_DeliveryboyController extends Zend_Controller_Action {

    protected $_auth;

    public function init() {

        $this->_auth = new My_Auth("admin");
        if (!$this->_auth->hasIdentity()) {
            $this->_helper->redirector('index', 'auth');
        }
        $this->_helper->layout()->setLayout('admin');
    }

    public function indexAction() {
        try {
            $request = $this->getRequest();
            $deliveryboyMapper = new Application_Model_DeliveryBoyMapper();
            $deliveryBoys = $deliveryboyMapper->getAllDeliveryBoys();
            if (!empty($deliveryBoys)) {
                $paginator = Zend_Paginator::factory($deliveryBoys);
                $paginator->setItemCountPerPage(100);
                $page = $this->getRequest()->getParam("page", 1);
                $paginator->setCurrentPageNumber($page);
            } else {
                $paginator = array();
            }
            $this->view->deliveryBoys = $paginator;
        } catch (Exception $ex) {
            $this->authorised = false;
            $this->view->hasMessage = true;
            $this->view->messageType = "danger";
            $this->view->message = $ex->getMessage();
        }
    }

    public function pickupAction() {
        
//        try
        {
            $request = $this->getRequest();
            $orderMapper = new Application_Model_OrdersMapper();
            $orders = $orderMapper->filter('','not_picked','','',date('Y-m-d'));
            //delivery boy list - time slot wise
            $deliveryBoys = $orderMapper->getOrderByDeliveryBoy(null, date('Y-m-d'),'pickup');
            if (!empty($orders)) {
                $paginator = Zend_Paginator::factory($orders);
                $paginator->setItemCountPerPage(100);
                $page = $this->getRequest()->getParam("page", 1);
                $paginator->setCurrentPageNumber($page);
            } else {
                $paginator = array();
            }
            $this->view->orders = $paginator;
            $this->view->deliveryBoys = $deliveryBoys;
            $this->view->timeSlot = $orderMapper->getTimeSlot();
        }
//         catch (Exception $ex) {
//            $this->authorised = false;
//            $this->view->hasMessage = true;
//            $this->view->messageType = "danger";
//            $this->view->message = $ex->getMessage();
//        }
    }

    public function deliveryAction() {
        try {
            $request = $this->getRequest();
            $orderMapper = new Application_Model_OrdersMapper();
            $orders = $orderMapper->filter('','not_delivered','',date('Y-m-d'),'');
            $deliveryBoys = $orderMapper->getOrderByDeliveryBoy(date('Y-m-d'), null,'delivery');
            if (!empty($orders)) {
                $paginator = Zend_Paginator::factory($orders);
                $paginator->setItemCountPerPage(100);
                $page = $this->getRequest()->getParam("page", 1);
                $paginator->setCurrentPageNumber($page);
            } else {
                $paginator = array();
            }
            $this->view->orders = $paginator;
            $this->view->deliveryBoys = $deliveryBoys;
            $this->view->timeSlot = $orderMapper->getTimeSlot();

        } catch (Exception $ex) {
            $this->authorised = false;
            $this->view->hasMessage = true;
            $this->view->messageType = "danger";
            $this->view->message = $ex->getMessage();
        }
    }

    public function allotorderAction() {
        try {
            $orderForm = new Application_Form_DeliveryallotForm();
            $this->view->form = $orderForm;
            $request = $this->getRequest();
            $orderMapper = new Application_Model_OrdersMapper();
            $orderModel = new Application_Model_Orders();
            if ($request->isPost()) {

                $request_type = $request->getParam("request_type", false);
                if ($request_type) {

                    if ($request_type == "add") {
                        $params = $request->getParams();

                        if ($orderForm->isValid($params)) {

                            $this->allotOrder($params);
                            $this->_redirect("admin/deliveryboy/allotorder");
                        } else {
                            $this->view->message = "Error occured while Adding. Please fill form correctly";
                            $this->view->hasMessage = true;
                            $this->view->messageType = "danger";
                        }
                    }
                }
            }
        } catch (Exception $ex) {
            $this->authorised = false;
            $this->view->hasMessage = true;
            $this->view->messageType = "danger";
            $this->view->message = $ex->getMessage();
        }
    }

    public function ajaxallotorderAction(){
        $request = $this->getRequest();
        $orderMapper = new Application_Model_OrdersMapper();
        $orderModel = new Application_Model_Orders();
        $ret = array('error'=>true,'message'=>'Oops some error occurred','success'=>false);
        $params = $this->getRequest()->getParams();
        if(isset($params['orders']) && isset($params['delboy_id']) && $params['isSingle'] == 'true'){
            $this->allotOrder($params, $params['allot_type']);
            $ret['error'] = false;
            $ret['success'] = true;
        }elseif($params['isSingle'] == 'false'){
            $delboyId = $params['delboy_id'];
            $this->allotMultipleOrder($params['orders'], $delboyId, $params['allot_type']);
            $ret['error'] = false;
            $ret['success'] = true;
        }
        echo json_encode($ret); die;
    }
    
    public function allotMultipleOrder($orderIds, $delboyId,$orderType = 'pickup'){
        foreach($orderIds as $orderId){
            $param['delboy_id'] = $delboyId;
            $param['orders'] = $orderId;
            $this->allotOrder($param, $orderType);
        }
    }
    
    public function allotOrder($params , $orderType = 'pickup'){
        $orderMapper = new Application_Model_OrdersMapper();
        $orderModel = new Application_Model_Orders();
        
        if($orderType == 'pickup')
            $orderModel->__set('order_pickup_boy', $params['delboy_id']);
        else
            $orderModel->__set('order_delivery_boy', $params['delboy_id']);
        
        $orderModel->__set('order_status', 'alloted');
        $orderModel->__set('order_id', $params['orders']);
        $orderMapper->allotOrder($orderModel, $orderType);
    }
}