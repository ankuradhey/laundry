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
            $deliveryboyMapper = new Application_Model_DeliveryboyMapper();
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
                            $orderModel->__set('order_delivery_boy', $params['delboy_id']);
                            $orderModel->__set('order_status', 'alloted');
                            $orderModel->__set('order_id', $params['orders']);
                            $orderMapper->allotOrder($orderModel);
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

}