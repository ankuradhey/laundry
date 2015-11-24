<?php

class MyAccountController extends Zend_Controller_Action {

    protected $_auth;

    public function init(){
        $this->_auth = $auth = TBS\Auth::getInstance();
    }

    public function indexAction() {

        try {

            $request = $this->getRequest();
            $request_type = $request->getParam("request_type", FALSE);
            $user = new Zend_Session_Namespace('userInfo');

            $this->view->user_img = $user->user_img;

            $user_id = @$user->user_id;
            $usersMapper = new Application_Model_UsersMapper();

            $user = $usersMapper->getUserById($user_id);

            if ($request->isPost()) {
                if ($request_type) {
                    if ($request_type == "update") {

                        $u_fname = $request->getParam("user_fname");
                        $u_lname = $request->getParam("user_lname");
                        $u_email = $request->getParam("user_email");
                        $u_address = $request->getParam("user_address");
                        $u_address_additional = $request->getParam("user_address_additional");

                        $u_locality = $request->getParam("user_locality");
                        $u_city = $request->getParam("user_city");
                        $u_state = $request->getParam("user_state");
                        $u_country = $request->getParam("user_country");
                        $u_number = $request->getParam("user_number");
                        $u_landmark = $request->getParam("user_landmark");

                        $user->__set("user_fname", $u_fname);
                        $user->__set("user_lname", $u_lname);
                        $user->__set("user_email", $u_email);
                        $user->__set("user_address", $u_address);
                        $user->__set("user_address_additional", $u_address_additional);

                        $user->__set("user_locality", $u_locality);
                        $user->__set("user_city", $u_city);
                        $user->__set("user_state", $u_state);
                        $user->__set("user_country", $u_country);
                        $user->__set("user_number", $u_number);

                        if ($u_landmark) {
                            $user->__set("user_landmark", $u_landmark);
                        } else {
                            $user->__set("user_landmark", "");
                        }
                        $isUpdated = $usersMapper->updateUser($user);

                        if (is_object($isUpdated) && $isUpdated->success) {

                            $this->view->hasMessage = true;
                            $this->view->messageType = "success";
                            $this->view->message = "Profile Updated successfully";
                        } else {

                            $this->view->hasMessage = true;
                            $this->view->messageType = "danger";
                            $this->view->message = "Error while updating";
                        }
                    } elseif ($request_type == "change_password") {

                        $pass = $request->getParam("pass");
                        $cpaas = $request->getParam("cpass");

                        $errors = array();
                        if (empty($pass)) {
                            $errors[] = "Password Should Not Be Empty";
                        }
                        if (empty($cpaas)) {
                            $errors[] = "Confirm Password Should Not Be Empty";
                        }

                        if ($pass != $cpaas) {
                            $this->view->hasMessage = true;
                            $this->view->messageType = "danger";
                            $this->view->message = "Password don't match";
                        } else {
                            $hashed_password = $user->__get("hashed_password");

                            $hashed_password = sha1($pass);
                            if (count($errors) == 0) {
                                $user->__set("hashed_password", $hashed_password);
                                if ($usersMapper->updateUser($user)) {
                                    $this->view->hasMessage = true;
                                    $this->view->messageType = "success";
                                    $this->view->message = "Password changed successfully";
                                } else {
                                    $this->view->hasMessage = true;
                                    $this->view->messageType = "danger";
                                    $this->view->message = "Error updating password. Try again";
                                }
                            } else {
                                $errorString = "";
                                foreach ($errors as $error) {
                                    $errorString .= $error . "<br/>";
                                }
                                $this->view->hasMessage = true;
                                $this->view->messageType = "danger";
                                $this->view->message = $errorString;
                            }
                        }
                    }
                }
            }

            $user_fname = $user->__get("user_fname");
            $user_lname = $user->__get("user_lname");
            $user_email = $user->__get("user_email");
            $user_address = $user->__get("user_address");
            $user_address_additional = $user->__get("user_address_additional");
            $user_locality = $user->__get("user_locality");
            $user_city = $user->__get("user_city");
            $user_state = $user->__get("user_state");
            $user_country = $user->__get("user_country");
            $user_number = $user->__get("user_number");
            $user_landmark = $user->__get("user_landmark");

            $this->view->fname = $user_fname;
            $this->view->lname = $user_lname;
            $this->view->email = $user_email;
            $this->view->address = $user_address;
            $this->view->address_additional = $user_address_additional;
            $this->view->locality = $user_locality;
            $this->view->city = $user_city;
            $this->view->phone = $user_number;
            $this->view->state = $user_state;
            $this->view->country = $user_country;
            $this->view->landmark = $user_landmark;
            $this->view->number = $user_number;
        } 
		catch (Exception $ex) {
            
        }

        $this->view->file_render = "account_profile";
    }

    public function scheduleAction(){
        $request = $this->getRequest();
        if($trackId = $request->getParam('trckId')){
            $this->view->deliveryType = 'Regular';
            $this->view->headlineText = 'Choose Your Collection and Delivery Time';
            $this->view->trackId = $trackId;
            $this->view->noFooter= 'true';
        }else{
            exit("invalid package");
        }
    }
   	
	public function orderssummaryAction() {
		
		
		$this->view->file_render = "account_orderssummary";
		$this->render("index");
        
		
    }
	 
    public function orderpackageAction(){
		
        if($this->getRequest()->isPost()){
            $post = $this->getRequest()->getPost();
            $userTrackId = $post['trackId'];
            $userInfo = new Zend_Session_Namespace('userInfo');
            $model = new Application_Model_OrdersMapper();
            
            //get usertrack details
            $userTrackModel = new Application_Model_UserTrackMapper();
            $userTrackDetail = $userTrackModel->getUserPackageById($userTrackId);
            
            //get order package details
            $packageDetails = new Application_Model_PackagesMapper();
            $packageDetails = $packageDetails->getPackageById($userTrackDetail->usertrack_package_id);
            $orders = new Application_Model_Orders();
            $orders->__set('order_user_id', $userInfo->user_id);
            $orders->__set('order_first_name', $userInfo->user_fname);
            $orders->__set('order_last_name', $userInfo->user_lname);
//            $orders->__set('order_user_email', 'anki.sharma@gmail.com');
            $orders->__set('order_address', $userTrackDetail->usertrack_house." ,".$userTrackDetail->usertrack_locality);
            $orders->__set('order_city', $userTrackDetail->usertrack_city);
            //TO DO - dynamic delivery type
            $orders->__set('order_delivery_type', 5);
            $orders->__set('order_pickup', date('Y-m-d',strtotime($post['pickupDate'])));
            $orders->__set('order_delivery', date('Y-m-d',strtotime($post['deliveryDate'])));
            $orders->__set('order_delivery_time', $post['deliveryTimeSlot']);
            $orders->__set('order_pickup_time', $post['pickupTimeSlot']);
//            $orders->__set('order_amount', $post['order_amount']);
            $orders->__set('order_payment_type', 'cash on delivery');
            $orders->__set('order_payment_status', 'paid');
            $orders->__set('order_service_type', $packageDetails->package_service_type);
            $orders->__set('order_type', 'package');
            
            if ($orderId = $model->addNewOrder($orders)) {
                $orderItem = new Application_Model_OrderItems();
                $orderItemModel = new Application_Model_OrderItemsMapper();
                //save product item
                $orderItem->__set('order_id',$orderId);
                $orderItem->__set('order_product_name',$packageDetails->__get('package_name'));
                $orderItem->__set('order_item_id',$packageDetails->__get('package_id'));
                $orderItem->__set('order_package_id',$packageDetails->__get('package_id'));
                $orderItem->__set('order_type','package');
//                $orderItem->__set('order_service_name',$laundryCart->itemservice[$item]);
//                $orderItem->__set('order_category_name',$laundryCart->itemcategory[$item]);
//                $orderItem->__set('unit_price',$laundryCart->itemprice[$item]);
//                $orderItem->__set('total_price',$laundryCart->itemprice[$item]*$quantity);
//                $orderItem->__set('quantity',$quantity);
                $orderItemModel->addNewOrderItem($orderItem);
                $this->_redirect('index/orderlist');
            } else {
                $this->view->message = "Error occured while adding. Please try again";
                $this->view->hasMessage = true;
                $this->view->messageType = "danger";
            }
//            $userTrackDetail->usertrack_package_id
//            echo '<pre>'; print_r($post); exit('Macro die');
        }else{
            
        }
    }
    
    public function subscriptionAction() {
        //get package list
        $userInfo = new Zend_Session_Namespace('userInfo');
        $userTrackMapper = new Application_Model_UserTrackMapper();
        $packages = $userTrackMapper->getUserPackages($userInfo->user_id);
        $this->view->fname = $userInfo->user_fname;
        $this->view->lname = $userInfo->user_lname;
        $this->view->noFooter = 'true';
        $this->view->packages = $packages?:array();
    }

    public function ordersAction(){

        $request = $this->getRequest();
        $user_email = $request->getParam("email");

        $ordersMapper = new Application_Model_OrdersMapper();
        $completed_orders = $ordersMapper->getOrdersByStatusUserEmail($user_email);
        $this->view->completed_orders = $completed_orders;

        $active_orders = $ordersMapper->getOrdersByOtherStatusUserEmail($user_email);
        $this->view->active_orders = $active_orders;
    }

    public function orderDetailsAction() {

        $request = $this->getRequest();
        $order_id = $request->getParam("id");

        $ordersMapper = new Application_Model_OrdersMapper();
        $orderItemsMapper = new Application_Model_OrderItemsMapper();
        $order = $ordersMapper->getOrderById($order_id);
        $this->view->order = $order;

        $items = $orderItemsMapper->getOrderItemByOrderId($order_id);
        $this->view->items = $items;
    }

}
