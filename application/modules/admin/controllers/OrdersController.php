<?php

class Admin_OrdersController extends Zend_Controller_Action {

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

            $order_form = new Application_Form_OrdersForm();
            $this->view->form = $order_form;

            $ordersMapper = new Application_Model_OrdersMapper();
            $order = new Application_Model_Orders();

            $request = $this->getRequest();
            if ($request->isPost()) {

                $request_type = $request->getParam("request_type", false);
                if ($request_type) {

                    if ($request_type == "add") {
                        $params = $request->getParams();
                        if ($order_form->isValid($params)) {
                            foreach ($params as $param => $value) {
                                if ($param == 'order_service_type') {
                                    $value = implode(',', $value);
                                }
                                $order->__set($param, $value);
                            }
                            if ($ordersMapper->addNewOrder($order)) {
                                //add package 
                                if ($params['order_type'] == 'package') {
                                    //get package details
                                    $packageDetails = new Application_Model_PackagesMapper();
                                    $packageDetails = $packageDetails->getPackageById($params['order_package']);
                                    $validity = $packageDetails->__get('validity');
                                    $userTrack = new Application_Model_UserTrack();
                                    $userTrackMapper = new Application_Model_UserTrackMapper();
                                    $userTrack->__set('usertrack_user_id', $params['order_user_id']);
                                    $userTrack->__set('track_type', 'package');
                                    $userTrack->__set('usertrack_package_id', $params['order_package']);
                                    $userTrack->__set('clothes_left', $packageDetails->__get('no_of_clothes'));
                                    $userTrack->__set('clothes_availed', $packageDetails->__get('no_of_clothes'));
                                    $userTrack->__set('pickups_left', $packageDetails->__get('no_of_pickups'));
                                    $userTrack->__set('pickups_availed', $packageDetails->__get('no_of_pickups'));
                                    $userTrack->__set('usertrack_start_date', date('Y-m-d'));
                                    $userTrack->__set('usertrack_expiry_date', date('Y-m-d', strtotime("+$validity Month " . date('Y-m-d'))));
                                    $userTrack->__set('usertrack_house', $params['order_address']);
//                                    $userTrack->__set('usertrack_locality', $laundryCart->address[1]);
                                    $userTrack->__set('usertrack_city', $params['order_city']);

                                    $userTrackMapper->addNewTrack($userTrack);
                                }

                                $this->view->message = "Order added successfully";
                                $this->view->hasMessage = true;
                                $this->view->messageType = "success";
                            } else {
                                $this->view->message = "Error occured while adding. Please try again";
                                $this->view->hasMessage = true;
                                $this->view->messageType = "danger";
                            }
                        } else {
                            $this->view->message = "Error occured while Adding. Please fill form correctly";
                            $this->view->hasMessage = true;
                            $this->view->messageType = "danger";
                        }
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
            $orders = $ordersMapper->getAllOrders();
            $this->view->orders = $orders;
            $this->authorised = true;
        } catch (Exception $ex) {
            $this->authorised = false;
            $this->view->hasMessage = true;
            $this->view->messageType = "danger";
            $this->view->message = $ex->getMessage();
        }
    }

    public function editAction() {
        try {
            $ordersMapper = new Application_Model_OrdersMapper();
            $orderItemsMapper = new Application_Model_OrderItemsMapper();

            $request = $this->getRequest();

            $order_id = $request->getParam("id");

            $this->view->order_id = $order_id;


            $orders = $ordersMapper->getOrderById($order_id);
            $this->view->orders = $orders;


            if ($orders) {
                if ($request->isPost()) {

                    $request_type = $request->getParam("request_type", false);

                    if ($request_type) {
                        if ($request_type == "update_order_info") {

                            $user_name = $request->getParam("user_name");
                            $user_address = $request->getParam("user_address");
                            $user_number = $request->getParam("user_number");
                            $delivery_type = $request->getParam("delivery_type");
                            $service_id = $request->getParam("service_id");

                            $errors = array();

                            if (empty($user_name)) {
                                $errors[] = "Username is empty";
                            }

                            if (empty($user_number)) {
                                $errors[] = "Phone number is empty";
                            }

                            if (empty($user_address)) {
                                $errors[] = "Address is empty";
                            }

                            if (empty($delivery_type)) {
                                $errors[] = "Delivering Type is not selected";
                            }

                            if (empty($service_id)) {
                                $errors[] = "Service is not selected";
                            }

                            if (count($errors) == 0) {

                                $orders->__set("user_fname", $user_name);
                                $orders->__set("user_phn_number", $user_number);
                                $orders->__set("user_address", $user_address);
                                $orders->__set("delivery_type", $delivery_type);
                                $orders->__set("service", $service_id);

                                if ($ordersMapper->updateOrder($orders)) {
                                    $this->view->hasMessage = true;
                                    $this->view->messageType = "success";
                                    $this->view->message = "Successfully update order info";
                                } else {
                                    $this->view->hasMessage = true;
                                    $this->view->messageType = "dnager";
                                    $this->view->message = "Error while updating the order info";
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
                        } elseif ($request_type == "update_qty") {
                            $total_price = 0;
                            $order_item_id = $request->getParam("order_item_id");
                            $qty = $request->getParam("qty");

                            $order_item = $orderItemsMapper->getOrderItemById($order_item_id);

                            if ($order_item) {

                                if ($qty) {

                                    $unit_price = $order_item->__get("unit_price");

                                    $new_price = $qty * $unit_price;

                                    $new_qty = $qty;


                                    $order_item->__set("quantity", $qty);
                                    $order_item->__set("total_price", $new_price);

                                    if ($orderItemsMapper->updateOrderItem($order_item)) {
                                        $orderItems = $orderItemsMapper->getOrderItemByOrderId($order_id);
                                        foreach ($orderItems as $item) {
                                            $total_price += $item->__get("total_price");
                                        }
                                        $orders->__set("order_total", $total_price);

                                        $ordersMapper->updateOrder($orders);
                                        $this->view->hasMessage = true;
                                        $this->view->messageType = "success";
                                        $this->view->message = "Successfully update Item quantity";
                                    } else {
                                        $this->view->hasMessage = true;
                                        $this->view->messageType = "success";
                                        $this->view->message = "error while updating qty";
                                    }
                                }
                            }
                        } elseif ($request_type == "delete") {
                            $item_id = $request->getParam("item_id");

                            $order_item = $orderItemsMapper->getOrderItemById($item_id);

                            $price = $order_item->__get("total_price");
                            $order_total = $orders->__get("order_total");

                            if ($orderItemsMapper->deleteOrderItemById($item_id)) {

                                $new_price = $order_total - $price;
                                $orders->__set("order_total", $new_price);
                                $ordersMapper->updateOrder($orders);

                                $this->view->hasMessage = true;
                                $this->view->messageType = "success";
                                $this->view->message = "Successfully deleted item";
                            } else {
                                $this->view->hasMessage = true;
                                $this->view->messageType = "success";
                                $this->view->message = "Error while deleting";
                            }
                        } elseif ($request_type == "order_item_add") {

                            $total_price = 0;
                            $category_id = $request->getParam("category_id");
                            $service_id = $request->getParam("service_id");
                            $item_id = $request->getParam("item_id");
                            $quantity = $request->getParam("quantity");

                            $itemPriceMapper = new Application_Model_ItemPriceMapper();
                            $orderItems = new Application_Model_OrderItems();

                            $errors = array();

                            if ($category_id == "none") {
                                $errors[] = "Please Select a category";
                            }
                            if ($service_id == "none") {
                                $errors[] = "Please Select a service";
                            }
                            if ($item_id == "none") {
                                $errors[] = "Please Select a item";
                            }
                            if (empty($quantity)) {
                                $errors[] = "Please enter quantity";
                            }

                            if (count($errors) == 0) {

                                $itemPrice = $itemPriceMapper->getItemPriceByItemId($item_id);
                                $item_price = $itemPrice->__get("price");

                                $price = $item_price * $quantity;


                                $orderItems->__set("order_id", $order_id);
                                $orderItems->__set("item_id", $item_id);
                                $orderItems->__set("quantity", $quantity);
                                $orderItems->__set("unit_price", $item_price);
                                $orderItems->__set("total_price", $price);

                                if ($orderItemsMapper->addNewOrderItem($orderItems)) {

                                    $orderItems = $orderItemsMapper->getOrderItemByOrderId($order_id);
                                    foreach ($orderItems as $item) {
                                        $total_price += $item->__get("total_price");
                                    }
                                    $orders->__set("order_total", $total_price);

                                    $ordersMapper->updateOrder($orders);

                                    $this->view->hasMessage = true;
                                    $this->view->messageType = "success";
                                    $this->view->message = "Successfully add";
                                } else {
                                    $this->view->hasMessage = true;
                                    $this->view->messageType = "danger";
                                    $this->view->message = "Error while add";
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


            $orderItems = $orderItemsMapper->getOrderItemByOrderId($order_id);
            $this->view->orderItems = $orderItems;

            $this->authorised = true;
        } catch (Exception $ex) {
            $this->authorised = false;
            $this->view->hasMessage = true;
            $this->view->messageType = "danger";
            $this->view->message = $ex->getMessage();
        }
    }

    public function summaryAction() {

        try {

            $request = $this->getRequest();
            $orderMapper = new Application_Model_OrdersMapper();

            if ($request->isPost()) {

                $request_type = $request->getParam("request_type", false);

                if ($request_type) {

                    if ($request_type == "filter") {

                        $user_email = $request->getParam("user_email");
                        $del_type = $request->getParam("delivery_type");
                        $service_id = $request->getParam("service_id");
                        $del_date = $request->getParam("delivery_date");

                        $pickup_date = $request->getParam("pickup_date");

                        $filter_orders = $orderMapper->filter($user_email, $del_type, $service_id, $del_date, $pickup_date);

                        if (!$filter_orders) {
                            $paginator = false;
                        } else {
                            $paginator = Zend_Paginator::factory($filter_orders);
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
                    } elseif ($request_type == "changestatus") {

                        $status = $request->getParam("status");
                        $order_ids = $request->getParam("order_ids");

                        $ids = explode(",", $order_ids);

                        foreach ($ids as $id) {
                            $order = $orderMapper->getOrderById($id);

                            $order->__set("order_status", $status);

                            if ($orderMapper->updateOrder($order)) {
                                $this->view->message = "Orders Updated successfully";
                                $this->view->hasMessage = true;
                                $this->view->messageType = "success";
                            } else {
                                $this->view->message = "Error occured while updating. Please try again";
                                $this->view->hasMessage = true;
                                $this->view->messageType = "danger";
                            }
                        }
                    } elseif ($request_type == "delete") {

                        $id = $request->getParam("id");
                        if ($orderMapper->deleteOrderById($id)) {
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
            } else {

                $orders = $orderMapper->getAllOrders();

                if (!empty($orders)) {
                    $paginator = Zend_Paginator::factory($orders);
                    $paginator->setItemCountPerPage(100);
                    $page = $this->getRequest()->getParam("page", 1);
                    $paginator->setCurrentPageNumber($page);
                } else {
                    $paginator = array();
                }
                $this->view->orders = $paginator;
            }
        } catch (Exception $ex) {
            $this->authorised = false;
            $this->view->hasMessage = true;
            $this->view->messageType = "danger";
            $this->view->message = $ex->getMessage();
        }
    }

    public function receiptAction() {

        $this->_helper->layout->disableLayout();
        error_reporting(0);
        $orderId = $this->_getParam("id");
        $ordersMapper = new Application_Model_OrdersMapper();

        $orderDetail = $ordersMapper->getOrderById($orderId);

        $orderItemsMapper = new Application_Model_OrderItemsMapper();
        $orderItems = $orderItemsMapper->getOrderItemByOrderId($orderId);

        if (empty($orderDetail)) {
            echo "Order Not Found";
            die;
        }

        $this->view->orderDetail = $orderDetail;
        $this->view->orderItems = $orderItems;
    }

    public function printreceiptAction() {

        $this->_helper->layout->disableLayout();
        error_reporting(0);
        $orderId = $this->_getParam("id");
        $ordersMapper = new Application_Model_OrdersMapper();

        $orderDetail = $ordersMapper->getOrderById($orderId);

        $orderItemsMapper = new Application_Model_OrderItemsMapper();
        $orderItems = $orderItemsMapper->getOrderItemByOrderId($orderId);

        if (empty($orderDetail)) {
            echo "Order Not Found";
            die;
        }

        $this->view->orderDetail = $orderDetail;
        $this->view->orderItems = $orderItems;
    }

}
