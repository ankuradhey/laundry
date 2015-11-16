<?php 

class Admin_IndexController extends Zend_Controller_Action {

    public function init() {
        $this->_auth = new My_Auth("admin");
        if (!$this->_auth->hasIdentity()) {
            $this->_helper->redirector('index', 'auth');
        }
        $this->_helper->layout()->setLayout('admin');
    }

    public function indexAction() {

        $request = $this->getRequest();

        $ordersMapper = new Application_Model_OrdersMapper();
        

        if ($request->isPost()) {
            $request_type = $request->getParam("request_type", false);
            if ($request_type) {


                if ($request_type == "filter") {

                    $user_email = $request->getParam("user_email");
                    $del_type = $request->getParam("delivery_type");
                    $service_id = $request->getParam("service_id");
                    $del_date = $request->getParam("delivery_date");

                    $pickup_date = $request->getParam("pickup_date");

                    $filter_orders = $ordersMapper->filter($user_email, $del_type, $service_id, $del_date, $pickup_date);
                    //print_r($filter_orders);exit;
                    if (!$filter_orders) {
                        $paginator = false;
                    } else {
                        $paginator = Zend_Paginator::factory($filter_orders);
                        //$paginator->setItemCountPerPage();
                        $paginator->setItemCountPerPage('100');
                        $page = $request->getParam("page", 1);
                        if ($page) {
                            $paginator->setCurrentPageNumber($page);
                        }
                    }

                    $this->view->orders = $paginator;
                    $this->view->user_email = $user_email;
                    $this->view->del_type = $del_type;
                    $this->view->service_id = $service_id;
                    $this->view->del_date = $del_date;
                    $this->view->pickup_date = $pickup_date;
                    
                } elseif ($request_type == "cancel_filter") {
                    $this->view->orders = $orders;
                } elseif ($request_type == "delete") {

                    $id = $request->getParam("id");
                    if ($ordersMapper->deleteOrderById($id)) {
                        $this->view->hasMessage = true;
                        $this->view->messageType = "success";
                        $this->view->message = "Order deleted successfully.";
                    } else {
                        $this->view->hasMessage = true;
                        $this->view->messageType = "danger";
                        $this->view->message = "Error occured while deleting. Please try again.";
                    }
                }
            }
        }
        $orders = $ordersMapper->getOrdersByStatus();
        if(empty($orders)){
            $paginator = null;
        }else{
            $paginator = Zend_Paginator::factory($orders);
            $paginator->setItemCountPerPage(10);
            $page = $this->getRequest()->getParam("page", 1);
            $paginator->setCurrentPageNumber($page);
        }
        $this->view->orders = $paginator;
    }

}
