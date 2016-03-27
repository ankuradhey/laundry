<?php

class CheckoutController extends Zend_Controller_Action {

    protected $_auth;

    public function init() {
        $this->_auth = new My_Auth("user");

        $this->_helper->layout()->setLayout('master');
    }

    public function indexAction() {

        $request = $this->getRequest();
        $request_type = $request->getParam("request_type", FALSE);

        if ($request_type) {
            if ($request_type == "order") {

                $delivery_type_name = $request->getParam("delivery_type_name");
                $service_id = $request->getParam("service_id");
                $date = $request->getParam("date");
                $time = $request->getParam("time");

                $delivery_date = $request->getParam("delivery_date");
                $delivery_time = $request->getParam("delivery_time");

                $errors = array();
                if (empty($delivery_type_name)) {
                    $errors[] = "Delivery Type Should Not Be Empty";
                }
                if (empty($service_id)) {
                    $errors[] = "Service Should Not Be Empty";
                }
                if (empty($date)) {
                    $errors[] = "Date Should Not Be Empty";
                }
                if (empty($time)) {
                    $errors[] = "Time Should Not Be Empty";
                }
                if (count($errors) == 0) {
                    $this->view->del_name = $delivery_type_name;
                    $this->view->service_id = $service_id;
                    $this->view->date = $date;
                    $this->view->time = $time;
                    $this->view->del_time = $delivery_time;
                    $this->view->del_date = $delivery_date;

                    $session = new Zend_Session_Namespace("order");
                    $session->del_name = $delivery_type_name;
                    $session->service_id = $service_id;
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

    public function checkoutAction() {
        $user_id = 0;
        if ($this->_auth->hasIdentity()) {
            $user_id = $this->_auth->getIdentity()->user_id;
            $this->view->user_id = $user_id;
        }


        $errors = array();

        $request = $this->getRequest();


        //$delivery_type = $request->getParam("delivery_type");
        //$service_name = $request->getParam("service_name");
        //$pickup_date = $request->getParam("pickup_date");
        //$pickup_time = $request->getParam("pickup_time");
        //$delivery_date = $request->getParam("del_date");
        //$delivery_time = $request->getParam("del_time");

        $date_session = new Zend_Session_Namespace("date");
        //print_r($_SESSION);exit;
        $pickup_date = $date_session->pick_date;
        $pickup_time = $date_session->pick_time;
        $delivery_date = $date_session->del_date;
        $delivery_time = $date_session->del_time;
        $service_name = $date_session->service_id;
        $delivery_type = $date_session->del_name;

        $user_package_id = $request->getParam("user_package_id");
        $this->view->user_package_id = $user_package_id;

        //$this->view->del_name = $delivery_type;
        //$this->view->service_id = $service_name;
        //$this->view->date = $pickup_date;
        //$this->view->time = $pickup_time;
        //$this->view->del_time = $delivery_time;
        //$this->view->del_date = $delivery_date;

        $request_type = $request->getParam("request_type", false);

        $addressMapper = new Application_Model_SavedAddressesMapper();
        $usersWalletMapper = new Application_Model_UserWalletMapper();
        $userWallet = new Application_Model_UserWallet();

        $payment_type = $request->getParam("payment_type");


        if ($user_id) {

            $credit_amount = $usersWalletMapper->getUserWalletCreditByUserId($user_id);
            $debit_amount = $usersWalletMapper->getUserWalletDebitByUserId($user_id);
            $wallet_amount = $credit_amount - $debit_amount;
            $wallet_session = new Zend_Session_Namespace("wallet");
            $wallet_session->amount = $wallet_amount;
            $this->view->wallet_amount = $wallet_amount;

            $userPackagesMapper = new Application_Model_UserPackagesMapper();
            $userPackages = $userPackagesMapper->getUserPackageByUserId($user_id);
            $this->view->userPackages = $userPackages;
        }


        if ($request_type) {

            if ($request_type == "item_details") {

                $session_quantity = $request->getParam("qty");
                $session_price = $request->getParam("price");
                $session_items = $request->getParam("items");
                //print_r($session_items);exit;
                $session = new Zend_Session_Namespace("order");
                //print_r($session_quantity);

                $total = 0;
                $i = 0;
                $quantity_total = 0;

                $actual_items = array();
                $actual_qty = array();
                $actual_price = array();
                foreach ($session_items as $item) {

                    $item_id = $item;
                    $qty = $session_quantity[$i];
                    $new_price = $session_price[$i];

                    if ($qty != 0) {
                        $actual_items[] = $item_id;
                        $actual_qty[] = $qty;
                        $actual_price[] = $new_price;
                    }
                    $item_total = $qty * $new_price;
                    $total += $item_total;
                    $quantity_total += $qty;
                    $i++;
                }
                $session->quantity = $actual_qty;
                $session->price = $actual_price;
                $session->items = $actual_items;
                $session->total = $total;
                $session->quantity_total = $quantity_total;


                //print_r($actual_items);exit;
                //print_r($actual_qty);exit;
                //$this->view->actual_qty = $actual_qty;
                //$this->view->actual_items = $actual_items;
                //print_r($_SESSION);
                //exit;
            }
            /* elseif ($request_type == "add_address") {
              $user_name = $request->getParam("user_name");
              $pincode = $request->getParam("pincode");
              $landmark = $request->getParam("landmark");
              $phone = $request->getParam("phone");
              $address = $request->getParam("address");
              $terms = $request->getParam("terms");
              $city = $request->getParam("city");
              $state = $request->getParam("state");

              if (!$terms) {
              $this->view->address_hasMessage = true;
              $this->view->messageType = "danger";
              $this->view->message = "Please check our term and condition";
              } else {
              if (empty($pincode)) {
              $errors[] = "Pincode is empty";
              }
              if (empty($address)) {
              $errors[] = "Address is empty";
              }
              if (empty($landmark)) {
              $errors[] = "Landmark is empty";
              }
              if (empty($city)) {
              $errors[] = "City is empty";
              }
              if (empty($state)) {
              $errors[] = "State is empty";
              }
              if (empty($phone)) {
              $errors[] = "Phone is empty";
              }

              if (count($errors) == 0) {

              $address_id = $request->getParam("address_id");

              if ($address_id) {
              $del_address = $addressMapper->getSavedAddressById($address_id);
              } else {
              $del_address = new Application_Model_SavedAddresses();
              }
              $usersMapper = new Application_Model_UsersMapper();
              $user = $usersMapper->getUserById($user_id);
              $del_address->__set("user_id", $user_id);
              $del_address->__set("address_pincode", $pincode);
              $del_address->__set("saved_address", $address);
              $del_address->__set("address_landmark", $landmark);
              $del_address->__set("contact_person_phn", $phone);
              $del_address->__set("contact_person_name", $user->__get("user_fname") . " " . $user->__get("user_lname"));
              $del_address->__set("address_city", $city);
              $del_address->__set("address_state", $state);
              $del_address->__set("address_street", "");
              $del_address->__set("address_locality", "");
              $del_address->__set("address_country", "");

              if ($address_id) {
              if ($addressMapper->updateSavedAddress($del_address)) {
              //$step_cart->address_id = $address_id;
              //$flag_step2 = TRUE;
              $this->view->address_hasMessage = true;
              $this->view->messageType = "success";
              $this->view->message = "Your new Address added sucessfully";
              } else {
              $this->view->address_hasMessage = true;
              $this->view->messageType = "danger";
              $this->view->message = "Some error while adding your addreess please fill the form correctlyadadakjdkas";
              }
              } else {
              $address_id = $addressMapper->addNewSavedAddress($del_address);
              if ($address_id) {
              //$step_cart->address_id = $address_id;
              //$flag_step2 = TRUE;
              $this->view->address_hasMessage = true;
              $this->view->messageType = "success";
              $this->view->message = "Your new Address added sucessfully";
              } else {
              $this->view->address_hasMessage = true;
              $this->view->messageType = "danger";
              $this->view->message = "Some error while adding your addreess please fill the form correctly";
              }
              }
              } else {
              $errorString = "";
              foreach ($errors as $error) {
              $errorString .= $error . "<br/>";
              }
              $this->view->address_hasMessage = true;
              $this->view->messageType = "danger";
              $this->view->message = $errorString;
              }
              }
              } */ elseif ($request_type == "payment_type") {

                $ordersMapper = new Application_Model_OrdersMapper();
                $orders = new Application_Model_Orders();
                $orderItemsMapper = new Application_Model_OrderItemsMapper();
                $orderItems = new Application_Model_OrderItems();
                $savedAddressMapper = new Application_Model_SavedAddressesMapper();



                $session = new Zend_Session_Namespace("order");

                $quantity = $session->quantity;
                $price = $session->price;
                $items = $session->items;
                //print_r($items);exit;
                $i = 0;



                if (empty($payment_type)) {
                    $errors[] = "Select a Payment Type";
                }


                if (count($errors) == 0) {

                    $add_session = new Zend_Session_Namespace("address");

                    $user_name = $add_session->username;
                    $pincode = $add_session->pincode;
                    $landmark = $add_session->landmark;
                    $city = $add_session->city;
                    $address = $add_session->address;
                    $state = $add_session->state;
                    $number = $add_session->phone;
                    $guest_email = $add_session->email;


                    $revised_amount = $request->getParam("revised_amount");
                    $discount_amount = $request->getParam("discount_amount");

                    //$number = $saved->__get("contact_person_phn");
                    //$address = $saved->__get("saved_address");
                    //$locality = $saved->__get("address_locality");
                    //$landmark = $saved->__get("address_landmark");
                    //$city = $saved->__get("address_city");
                    //$state- = $saved->__get("address_state");
                    //$pincode = $saved->__get("address_pincode");
                    //$country = $saved->__get("address_country");
                    if ($user_id) {

                        $usersMapper = new Application_Model_UsersMapper();
                        $user = $usersMapper->getUserById($user_id);

                        $fname = $user->__get("user_fname");
                        $lname = $user->__get("user_lname");
                        $user_email = $user->__get("user_email");
                        $number = $user->__get("user_number");

                        $orders->__set("user_fname", $fname);
                        $orders->__set("user_lname", $lname);
                        $orders->__set("user_email", $user_email);
                        $orders->__set("user_phn_number", $number);
                        $orders->__set("user_address", $address);
                        $orders->__set("address_locality", "");
                        $orders->__set("address_street", "");
                        $orders->__set("address_landmark", $landmark);
                        $orders->__set("address_city", $city);
                        $orders->__set("address_state", $state);
                        $orders->__set("address_country", "India");
                        $orders->__set("address_pincode", $pincode);
                        $orders->__set("payment_method", $payment_type);
                        $orders->__set("payment_status", "Unpaid");
                        $orders->__set("order_status", "New");
                        $orders->__set("order_total", 0);
                        $orders->__set("delivery_type", $delivery_type);
                        $orders->__set("service", $service_name);
                        //echo $pickup_date;exit;
                        $zend_date = new Zend_Date($pickup_date, "dd/MM/yyyy");
                        $pickup_date_new = $zend_date->toString("dd-MMM-yyyy");
                        $orders->__set("pickup_date", $pickup_date_new);
                        $orders->__set("pickup_time", $pickup_time);


                        if ($delivery_type == "Regular") {

                            $zendDate = new Zend_Date($delivery_date, "dd/MM/yyyy");
                            $delivery_date_new = $zendDate->toString("dd-MMM-yyyy");
                        } else {
                            $z_date = $zend_date->addDay(1);
                            $delivery_date_new = $z_date->toString("dd-MMM-yyyy");
                        }

                        if ($delivery_type == "Regular") {

                            $delivery_time = $delivery_time;
                        } else {
                            //$zendDate = new Zend_Date($pickup_time);
                            $delivery_time = "";
                            //echo $delivery_time;exit;
                        }

                        $orders->__set("delivery_date", $delivery_date_new);
                        $orders->__set("delivery_time", $delivery_time);
                    } else {

                        $orders->__set("user_fname", $user_name);
                        $orders->__set("user_lname", "");
                        $orders->__set("user_email", $guest_email);
                        $orders->__set("user_phn_number", $number);
                        $orders->__set("user_address", $address);
                        $orders->__set("address_locality", "");
                        $orders->__set("address_street", "");
                        $orders->__set("address_landmark", $landmark);
                        $orders->__set("address_city", $city);
                        $orders->__set("address_state", $state);
                        $orders->__set("address_country", "India");
                        $orders->__set("address_pincode", $pincode);
                        $orders->__set("payment_method", $payment_type);
                        $orders->__set("payment_status", "Unpaid");
                        $orders->__set("order_status", "New");
                        $orders->__set("order_total", 0);
                        $orders->__set("delivery_type", $delivery_type);
                        $orders->__set("service", $service_name);
                        $zend_date = new Zend_Date($pickup_date, "dd/MM/yyyy");
                        $pickup_date_new = $zend_date->toString("dd-MMM-yyyy");
                        $orders->__set("pickup_date", $pickup_date_new);
                        $orders->__set("pickup_time", $pickup_time);

                        if ($delivery_type == "Regular") {
                            $zendDate = new Zend_Date($delivery_date, "dd/MM/yyyy");
                            $delivery_date_new = $zendDate->toString("dd-MMM-yyyy");
                        } else {
                            $z_date = $zend_date->addDay(1);
                            $delivery_date_new = $z_date->toString("dd-MMM-yyyy");
                        }

                        if ($delivery_type == "Regular") {

                            $delivery_time = $delivery_time;
                        } else {
                            //$zendDate = new Zend_Date($pickup_time);
                            $delivery_time = "";
                            //echo $delivery_time;exit;
                        }

                        $orders->__set("delivery_date", $delivery_date_new);
                        $orders->__set("delivery_time", $delivery_time);
                    }

                    if ($payment_type == "wallet") {
                        $session = new Zend_Session_Namespace("order");
                        $total = $session->total;
                        $q_total = $total = $session->quantity_total;

                        if ($total > $wallet_amount) {
                            $this->view->hasMessage = true;
                            $this->view->messageType = "Danger";
                            $this->view->message = "Insufficient Fund in your Wallet.";
                        } else {
                            $order_id = $ordersMapper->addNewOrder($orders);


                            $total = 0;
                            foreach ($items as $item) {

                                $item_id = $item;
                                $qty = $quantity[$i];
                                $new_price = $price[$i];

                                $item_total = $qty * $new_price;
                                $total += $item_total;
                                $i++;



                                if ($order_id) {
                                    $orderItems->__set("order_id", $order_id);
                                    $orderItems->__set("item_id", $item_id);
                                    $orderItems->__set("quantity", $qty);
                                    $orderItems->__set("total_price", $item_total);
                                    $orderItems->__set("unit_price", $new_price);

                                    ($orderItemsMapper->addNewOrderItem($orderItems));
                                }
                            }

                            $orders = $ordersMapper->getOrderById($order_id);

                            if ($total <= 100) {
                                $total_new = $total + 50;
                            } else {
                                $total_new = $total;
                            }

                            $orders->__set("order_total", $total_new);
                            $orders->__set("payment_status", "Paid");
                            $service = $orders->__get("service");
                            $d_date = $orders->__get("delivery_date");

                            if ($discount_amount && $revised_amount) {
                                $orders->__set("discount_price", $discount_amount);
                                $orders->__set("revised_price", $revised_amount);
                                
                                $total_new = $revised_amount;
                            }


                            $sms = "Dear " . $fname . ", Your order " . $order_id . " with QTY " . $q_total . " and amount " . $total_new . " will be ready on " . $d_date . ". Thanks LaundryWala";

                            $sms_result = $this->_smsNotification($number, $sms);

                            $userWallet->__set("user_id", $user_id);
                            $userWallet->__set("entry_type", "DEBIT");
                            $userWallet->__set("entry_amount", $total);

                            $usersWalletMapper->addNewUserWallet($userWallet);

                            $subject = "Order Details From LaundryWala";

                            $message = "<table style='font-size:12px;'>";
                            $message.= "<tbody>";
                            $message.= "<tr><td>Dear Customer ,<br><br></td></tr>";
                            $message.= "<tr><td>Thank you for your association with LaundryWala.We are pleased to provide you details around your recent transaction with us.<br><br></td></tr>";
                            $message.= "<tr><td><strong>Invoice No :</strong>" . $order_id . "</td></tr>";
                            $message.= "<tr><td><strong>Customer Name :</strong>" . $fname . "</td></tr>";
                            $message.= "<tr><td><strong>Address :</strong>" . $address . "</td></tr>";
                            $message.= "<tr><td><strong>Phone No. :</strong><a>" . $number . "</a></td></tr>";

                            if ($orders) {
                                $email = $orders->__get("user_email");
                                $address = $orders->__get("user_address");
                                $city = $orders->__get("address_city");
                                $state = $orders->__get("address_state");
                                $pincode = $orders->__get("address_pincode");
                                $date = $orders->__get("timestamp");
                                $zendDate = new Zend_Date($date, "dd-MMM-yyyy");
                                $date_new = $zendDate->toString("dd-MMM-yyyy");
                                $number = $orders->__get("user_phn_number");
                                $method = $orders->__get("payment_method");
                                $del_date = $orders->__get("delivery_date");


                                $message.= "<tr><td><strong>Booking Date :</strong>" . $date_new . "<br><strong>Payment Mode:</strong>" . $method . "</td></tr>";
                                $message.= "<tr><td><strong>Total Amount Due Rs.:</strong>" . $total_new . "<br></td></tr>";
                                $message.= "<tr><td><strong>Due Delivery Date :</strong>" . $del_date . "</td></tr>";
                                $message.= "<tr><td><strong>Total Pcs. :</strong>" . $q_total . "</td></tr>";
                                $message.= "<tr style='font-size:12px'>";
                                $message.= "<td colspan='4'>";
                                $message.= "<table style='width:7.9in'>";
                                $message.= "<tbody>";
                                $message.= "<tr>";
                                $message.= "<td style='width:0.5in;font-weight:bold;font-size:12px'>S.No.</td>";
                                $message.= "<td style='width:0.5in;font-weight:bold;font-size:12px'>Qty</td>";
                                $message.= "<td style='width:5.0in;font-weight:bold;font-size:12px' align='left'>Particular's</td>";
                                $message.= "<td style='width:1.0in;font-weight:bold' align='right'></td>";
                                $message.= "<td style='width:1.0in;font-weight:bold;font-size:12px' align='right'>Amount</td>";
                                $message.= "</tr>";
                                $message.= "<tr style='font-size:9px'>";
                                $message.= "<td colspan='5' style='font-weight:bold;border-top-style:solid;border-top-width:thin;border-top-color:#000000'></td>";
                                $message.= "</tr>";

                                $orderItemsMapper = new Application_Model_OrderItemsMapper();
                                $items_new = $orderItemsMapper->getOrderItemByOrderId($orders->__get("order_id"));

                                $i = 1;
                                if ($items_new) {
                                    foreach ($items_new as $item) {
                                        $itemsMapper = new Application_Model_ItemsMapper();
                                        $item_name = $itemsMapper->getItemById($item->__get("item_id"));
                                        if ($item_name) {
                                            $name = $item_name->__get("item_name");
                                        } else {
                                            $name = "None";
                                        }
                                        $message.= "<tr style='font-size:12px'>";
                                        $message.= "<td>" . $i++ . "</td>";
                                        $message.= "<td>" . $item->__get("quantity") . "</td>";
                                        $message.= "<td colspan='2'><span style='font-family:arial black;font-size:11px'>" . $name . "</span></td>";
                                        //$message.= "<td align='right'></td>";
                                        $message.= "<td align='right'>Rs." . $item->__get("total_price") . "&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;</td>";
                                        $message.= "</tr>";
                                    }
                                }
                            }
                            $message.= "<tr style='font-size:10px'><td colspan='5'></td></tr>";
                            $message.= "<tr style='font-size:10px'>";
                            $message.= "<td colspan='2'></td>";
                            $message.= "<td align='right' style='font-size:12px;font-weight:bold'>Subtotal</td>";
                            $message.= "<td align='right' style='font-size:12px;font-weight:bold'>Rs." . $total . "</td>";
                            $message.= "</tr>";
                            if ($total <= 100) {
                                $message.= "<tr style='font-size:10px'>";
                                $message.= "<td colspan='3'></td>";
                                $message.= "<td align='right' style='font-size:12px;font-weight:bold'>Amount after Delivery Charges</td>";
                                $message.= "<td align='right' style='font-size:12px;font-weight:bold'>Rs." . $total_new . "</td>";
                                $message.= "</tr>";
                            } else {
                                $message.= "<tr style='font-size:10px'>";
                                $message.= "<td colspan='3'></td>";
                                $message.= "<td align='right' style='font-size:12px;font-weight:bold'>Amount after Discount</td>";
                                if ($discount_amount) {
                                    $message.= "<td align='right' style='font-size:12px;font-weight:bold'>Rs." . $revised_amount . "</td>";
                                } else {
                                    $message.= "<td align='right' style='font-size:12px;font-weight:bold'>Rs." . $total_new . "</td>";
                                }

                                $message.= "</tr>";
                            }

                            $message.= "<tr style='font-size:10px'>";
                            $message.= "<td colspan='3'></td>";
                            $message.= "<td align='right' style='font-size:12px;font-weight:bold'><br></td>";
                            $message.= "<td align='right' style='font-size:12px;font-weight:bold'><br></td>";
                            $message.= "</tr>";
                            $message.= "<tr><td nowrap='' colspan='3'>Assuring you of our best service.</td></tr>";
                            $message.= "<tr><td colspan='3'>Warm Regards.</td></tr>";
                            $message.= "<tr><td colspan='3'>LaundryWala</td></tr>";
                            $message.= "</tbody></table></td></tr></tbody></table>";

                            $this->getMailAction($email, $subject, $message);
                            $this->getMailAction("info@laundrywala.co.in", $subject, $message);



                            if ($ordersMapper->updateOrder($orders)) {
                                $this->_helper->redirector('notification', 'checkout');
                                $this->view->hasMessage = true;
                                $this->view->messageType = "success";
                                $this->view->message = "Your Order added sucessfully";
                            } else {
                                $this->view->hasMessage = true;
                                $this->view->messageType = "danger";
                                $this->view->message = "Error";
                            }
                        }
                    } elseif ($payment_type == "package") {

                        $session = new Zend_Session_Namespace("order");
                        $total = $session->total;
                        $quantity_total = $session->quantity_total;

                        if ($user_package_id) {

                            $userPackage = $userPackagesMapper->getUserPackageById($user_package_id);
                            if ($userPackage) {

                                $number_clothes = $userPackage->__get("clothes_left");
                                $number_pickups = $userPackage->__get("pickups_left");
                                if ($quantity_total > $number_clothes) {
                                    $this->view->hasMessage = true;
                                    $this->view->messageType = "Danger";
                                    $this->view->message = "Insufficient Number of clothes.";
                                } else {
                                    $order_id = $ordersMapper->addNewOrder($orders);


                                    $total = 0;
                                    foreach ($items as $item) {

                                        $item_id = $item;
                                        $qty = $quantity[$i];
                                        $new_price = $price[$i];

                                        $item_total = $qty * $new_price;
                                        $total += $item_total;
                                        $i++;

                                        if ($order_id) {
                                            $orderItems->__set("order_id", $order_id);
                                            $orderItems->__set("item_id", $item_id);
                                            $orderItems->__set("quantity", $qty);
                                            $orderItems->__set("total_price", $item_total);
                                            $orderItems->__set("unit_price", $new_price);

                                            ($orderItemsMapper->addNewOrderItem($orderItems));
                                        }
                                    }

                                    $orders = $ordersMapper->getOrderById($order_id);

                                    if ($total <= 100) {
                                        $total_new = $total + 50;
                                    } else {
                                        $total_new = $total;
                                    }

                                    $orders->__set("order_total", $total_new);
                                    $orders->__set("payment_status", "Paid");
                                    $service = $orders->__get("service");
                                    $d_date = $orders->__get("delivery_date");

                                    if ($discount_amount && $revised_amount) {
                                        $orders->__set("discount_price", $discount_amount);
                                        $orders->__set("revised_price", $revised_amount);
                                        
                                        $total_new = $revised_amount;
                                    }

                                    $sms = "Dear " . $fname . ", Your order " . $order_id . " with QTY " . $quantity_total . " and amount " . $total_new . " will be ready on " . $d_date . ". Thanks LaundryWala";

                                    //$sms_result = $this->_smsNotification($number, $sms);

                                    $clothes_left = $number_clothes - $quantity_total;
                                    $pickups_left = $number_pickups - 1;
                                    $userPackage->__set("clothes_left", $clothes_left);
                                    $userPackage->__set("pickups_left", $pickups_left);

                                    $userPackagesMapper->updateUserPackage($userPackage);

                                    $subject = "Order Details From LaundryWala";

                                    $message = "<table style='font-size:12px;'>";
                                    $message.= "<tbody>";
                                    $message.= "<tr><td>Dear Customer ,<br><br></td></tr>";
                                    $message.= "<tr><td>Thank you for your association with LaundryWala.We are pleased to provide you details around your recent transaction with us.<br><br></td></tr>";
                                    $message.= "<tr><td><strong>Invoice No :</strong>" . $order_id . "</td></tr>";
                                    $message.= "<tr><td><strong>Customer Name :</strong>" . $fname . "</td></tr>";
                                    $message.= "<tr><td><strong>Address :</strong>" . $address . "</td></tr>";
                                    $message.= "<tr><td><strong>Phone No. :</strong><a>" . $number . "</a></td></tr>";

                                    if ($orders) {
                                        $email = $orders->__get("user_email");
                                        $address = $orders->__get("user_address");
                                        $city = $orders->__get("address_city");
                                        $state = $orders->__get("address_state");
                                        $pincode = $orders->__get("address_pincode");
                                        $date = $orders->__get("timestamp");
                                        $zendDate = new Zend_Date($date, "dd-MMM-yyyy");
                                        $date_new = $zendDate->toString("dd-MMM-yyyy");
                                        $number = $orders->__get("user_phn_number");
                                        $method = $orders->__get("payment_method");
                                        $del_date = $orders->__get("delivery_date");


                                        $message.= "<tr><td><strong>Booking Date :</strong>" . $date_new . "<br><strong>Payment Mode:</strong>" . $method . "</td></tr>";
                                        $message.= "<tr><td><strong>Total Amount Due Rs.:</strong>" . $total_new . "<br></td></tr>";
                                        $message.= "<tr><td><strong>Due Delivery Date :</strong>" . $del_date . "</td></tr>";
                                        $message.= "<tr><td><strong>Total Pcs. :</strong>" . $quantity_total . "</td></tr>";
                                        $message.= "<tr style='font-size:12px'>";
                                        $message.= "<td colspan='4'>";
                                        $message.= "<table style='width:7.9in'>";
                                        $message.= "<tbody>";
                                        $message.= "<tr>";
                                        $message.= "<td style='width:0.5in;font-weight:bold;font-size:12px'>S.No.</td>";
                                        $message.= "<td style='width:0.5in;font-weight:bold;font-size:12px'>Qty</td>";
                                        $message.= "<td style='width:5.0in;font-weight:bold;font-size:12px' align='left'>Particular's</td>";
                                        $message.= "<td style='width:1.0in;font-weight:bold' align='right'></td>";
                                        $message.= "<td style='width:1.0in;font-weight:bold;font-size:12px' align='right'>Amount</td>";
                                        $message.= "</tr>";
                                        $message.= "<tr style='font-size:9px'>";
                                        $message.= "<td colspan='5' style='font-weight:bold;border-top-style:solid;border-top-width:thin;border-top-color:#000000'></td>";
                                        $message.= "</tr>";

                                        $orderItemsMapper = new Application_Model_OrderItemsMapper();
                                        $items_new = $orderItemsMapper->getOrderItemByOrderId($orders->__get("order_id"));

                                        $i = 1;
                                        if ($items_new) {
                                            foreach ($items_new as $item) {
                                                $itemsMapper = new Application_Model_ItemsMapper();
                                                $item_name = $itemsMapper->getItemById($item->__get("item_id"));
                                                if ($item_name) {
                                                    $name = $item_name->__get("item_name");
                                                } else {
                                                    $name = "None";
                                                }
                                                $message.= "<tr style='font-size:12px'>";
                                                $message.= "<td>" . $i++ . "</td>";
                                                $message.= "<td>" . $item->__get("quantity") . "</td>";
                                                $message.= "<td><span style='font-family:arial black;font-size:11px'>" . $name . "</span></td>";
                                                $message.= "<td align='right'></td>";
                                                $message.= "<td align='right'>Rs." . $item->__get("total_price") . "&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;</td>";
                                                $message.= "</tr>";
                                            }
                                        }
                                    }
                                    $message.= "<tr style='font-size:10px'><td colspan='5'></td></tr>";
                                    $message.= "<tr style='font-size:10px'>";
                                    $message.= "<td colspan='3'></td>";
                                    $message.= "<td align='right' style='font-size:12px;font-weight:bold'>Subtotal</td>";
                                    $message.= "<td align='right' style='font-size:12px;font-weight:bold'>Rs." . $total . "</td>";
                                    $message.= "</tr>";
                                    if ($total <= 100) {
                                        $message.= "<tr style='font-size:10px'>";
                                        $message.= "<td colspan='3'></td>";
                                        $message.= "<td align='right' style='font-size:12px;font-weight:bold'>Amount after Delivery Charges</td>";
                                        $message.= "<td align='right' style='font-size:12px;font-weight:bold'>Rs." . $total_new . "</td>";
                                        $message.= "</tr>";
                                    } else {
                                        $message.= "<tr style='font-size:10px'>";
                                        $message.= "<td colspan='3'></td>";
                                        $message.= "<td align='right' style='font-size:12px;font-weight:bold'>Amount after Discount</td>";
                                        if ($discount_amount) {
                                            $message.= "<td align='right' style='font-size:12px;font-weight:bold'>Rs." . $revised_amount . "</td>";
                                        } else {
                                            $message.= "<td align='right' style='font-size:12px;font-weight:bold'>Rs." . $total_new . "</td>";
                                        }

                                        $message.= "</tr>";
                                    }
                                    $message.= "<tr style='font-size:10px'>";
                                    $message.= "<td colspan='3'></td>";
                                    $message.= "<td align='right' style='font-size:12px;font-weight:bold'><br></td>";
                                    $message.= "<td align='right' style='font-size:12px;font-weight:bold'><br></td>";
                                    $message.= "</tr>";
                                    $message.= "<tr><td nowrap='' colspan='3'>Assuring you of our best service.</td></tr>";
                                    $message.= "<tr><td colspan='3'>Warm Regards.</td></tr>";
                                    $message.= "<tr><td colspan='3'>LaundryWala</td></tr>";
                                    $message.= "</tbody></table></td></tr></tbody></table>";

                                    $this->getMailAction($email, $subject, $message);
                                    $this->getMailAction("info@laundrywala.co.in", $subject, $message);


                                    if ($ordersMapper->updateOrder($orders)) {
                                        $this->_helper->redirector('notification', 'checkout');
                                        $this->view->hasMessage = true;
                                        $this->view->messageType = "success";
                                        $this->view->message = "Your Order added sucessfully";
                                    } else {
                                        $this->view->hasMessage = true;
                                        $this->view->messageType = "danger";
                                        $this->view->message = "Error";
                                    }
                                }
                            }
                        } else {
                            echo "<script>alert('No Package Selected or Available');</script>";
                        }



                        //print_r($userPackage);exit;
                    } elseif ($payment_type == "online-payment") {

                        $session = new Zend_Session_Namespace("order");
                        $total = $session->total;
                        $q_total = $total = $session->quantity_total;

                        $order_id = $ordersMapper->addNewOrder($orders);


                        $total = 0;
                        foreach ($items as $item) {

                            $item_id = $item;
                            $qty = $quantity[$i];
                            $new_price = $price[$i];

                            $item_total = $qty * $new_price;
                            $total += $item_total;
                            $i++;

                            if ($order_id) {
                                $orderItems->__set("order_id", $order_id);
                                $orderItems->__set("item_id", $item_id);
                                $orderItems->__set("quantity", $qty);
                                $orderItems->__set("total_price", $item_total);
                                $orderItems->__set("unit_price", $new_price);

                                ($orderItemsMapper->addNewOrderItem($orderItems));
                            }
                        }

                        $orders = $ordersMapper->getOrderById($order_id);

                        if ($total <= 100) {
                            $total_new = $total + 50;
                        } else {
                            $total_new = $total;
                        }

                        $orders->__set("order_total", $total_new);
                        $orders->__set("payment_status", "Unpaid");

                        $service = $orders->__get("service");
                        $d_date = $orders->__get("delivery_date");

                        if ($discount_amount && $revised_amount) {
                            $orders->__set("discount_price", $discount_amount);
                            $orders->__set("revised_price", $revised_amount);
                            
                            $total_new = $revised_amount;
                        }

                        $sms = "Dear " . $fname . ", Your order " . $order_id . " with QTY " . $q_total . " and amount " . $total_new . " will be ready on " . $d_date . ". Thanks LaundryWala";

                        $sms_result = $this->_smsNotification($number, $sms);

                        $subject = "Order Details From LaundryWala";

                        $message = "<table style='font-size:12px;'>";
                        $message.= "<tbody>";
                        $message.= "<tr><td>Dear Customer ,<br><br></td></tr>";
                        $message.= "<tr><td>Thank you for your association with LaundryWala.We are pleased to provide you details around your recent transaction with us.<br><br></td></tr>";
                        $message.= "<tr><td><strong>Invoice No :</strong>" . $order_id . "</td></tr>";
                        $message.= "<tr><td><strong>Customer Name :</strong>" . $fname . "</td></tr>";
                        $message.= "<tr><td><strong>Address :</strong>" . $address . "</td></tr>";
                        $message.= "<tr><td><strong>Phone No. :</strong><a>" . $number . "</a></td></tr>";

                        if ($orders) {
                            $email = $orders->__get("user_email");
                            $address = $orders->__get("user_address");
                            $city = $orders->__get("address_city");
                            $state = $orders->__get("address_state");
                            $pincode = $orders->__get("address_pincode");
                            $date = $orders->__get("timestamp");
                            $zendDate = new Zend_Date($date, "dd-MMM-yyyy");
                            $date_new = $zendDate->toString("dd-MMM-yyyy");
                            $number = $orders->__get("user_phn_number");
                            $method = $orders->__get("payment_method");
                            $del_date = $orders->__get("delivery_date");


                            $message.= "<tr><td><strong>Booking Date :</strong>" . $date_new . "<br><strong>Payment Mode:</strong>" . $method . "</td></tr>";
                            $message.= "<tr><td><strong>Total Amount Due Rs.:</strong>" . $total_new . "<br></td></tr>";
                            $message.= "<tr><td><strong>Due Delivery Date :</strong>" . $del_date . "</td></tr>";
                            $message.= "<tr><td><strong>Total Pcs. :</strong>" . $q_total . "</td></tr>";
                            $message.= "<tr style='font-size:12px'>";
                            $message.= "<td colspan='4'>";
                            $message.= "<table style='width:7.9in'>";
                            $message.= "<tbody>";
                            $message.= "<tr>";
                            $message.= "<td style='width:0.5in;font-weight:bold;font-size:12px'>S.No.</td>";
                            $message.= "<td style='width:0.5in;font-weight:bold;font-size:12px'>Qty</td>";
                            $message.= "<td style='width:5.0in;font-weight:bold;font-size:12px' align='left'>Particular's</td>";
                            $message.= "<td style='width:1.0in;font-weight:bold' align='right'></td>";
                            $message.= "<td style='width:1.0in;font-weight:bold;font-size:12px' align='right'>Amount</td>";
                            $message.= "</tr>";
                            $message.= "<tr style='font-size:9px'>";
                            $message.= "<td colspan='5' style='font-weight:bold;border-top-style:solid;border-top-width:thin;border-top-color:#000000'></td>";
                            $message.= "</tr>";

                            $orderItemsMapper = new Application_Model_OrderItemsMapper();
                            $items_new = $orderItemsMapper->getOrderItemByOrderId($orders->__get("order_id"));

                            $i = 1;
                            if ($items_new) {
                                foreach ($items_new as $item) {
                                    $itemsMapper = new Application_Model_ItemsMapper();
                                    $item_name = $itemsMapper->getItemById($item->__get("item_id"));
                                    if ($item_name) {
                                        $name = $item_name->__get("item_name");
                                    } else {
                                        $name = "None";
                                    }
                                    $message.= "<tr style='font-size:12px'>";
                                    $message.= "<td>" . $i++ . "</td>";
                                    $message.= "<td>" . $item->__get("quantity") . "</td>";
                                    $message.= "<td><span style='font-family:arial black;font-size:11px'>" . $name . "</span></td>";
                                    $message.= "<td align='right'></td>";
                                    $message.= "<td align='right'>Rs." . $item->__get("total_price") . "&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;</td>";
                                    $message.= "</tr>";
                                }
                            }
                        }
                        $message.= "<tr style='font-size:10px'><td colspan='5'></td></tr>";
                        $message.= "<tr style='font-size:10px'>";
                        $message.= "<td colspan='3'></td>";
                        $message.= "<td align='right' style='font-size:12px;font-weight:bold'>Subtotal</td>";
                        $message.= "<td align='right' style='font-size:12px;font-weight:bold'>Rs." . $total . "</td>";
                        $message.= "</tr>";
                        if ($total <= 100) {
                            $message.= "<tr style='font-size:10px'>";
                            $message.= "<td colspan='3'></td>";
                            $message.= "<td align='right' style='font-size:12px;font-weight:bold'>Amount after Delivery Charges</td>";
                            $message.= "<td align='right' style='font-size:12px;font-weight:bold'>Rs." . $total_new . "</td>";
                            $message.= "</tr>";
                        } else {
                            $message.= "<tr style='font-size:10px'>";
                            $message.= "<td colspan='3'></td>";
                            $message.= "<td align='right' style='font-size:12px;font-weight:bold'>Amount after Discount</td>";
                            if ($discount_amount) {
                                $message.= "<td align='right' style='font-size:12px;font-weight:bold'>Rs." . $revised_amount . "</td>";
                            } else {
                                $message.= "<td align='right' style='font-size:12px;font-weight:bold'>Rs." . $total_new . "</td>";
                            }

                            $message.= "</tr>";
                        }
                        $message.= "<tr style='font-size:10px'>";
                        $message.= "<td colspan='3'></td>";
                        $message.= "<td align='right' style='font-size:12px;font-weight:bold'><br></td>";
                        $message.= "<td align='right' style='font-size:12px;font-weight:bold'><br></td>";
                        $message.= "</tr>";
                        $message.= "<tr><td nowrap='' colspan='3'>Assuring you of our best service.</td></tr>";
                        $message.= "<tr><td colspan='3'>Warm Regards.</td></tr>";
                        $message.= "<tr><td colspan='3'>LaundryWala</td></tr>";
                        $message.= "</tbody></table></td></tr></tbody></table>";

                        $this->getMailAction($email, $subject, $message);
                        $this->getMailAction("info@laundrywala.co.in", $subject, $message);



                        if ($ordersMapper->updateOrder($orders)) {
                            $this->view->hasMessage = true;
                            $this->view->messageType = "success";
                            $this->view->message = "Your Order added sucessfully";
                        } else {
                            $this->view->hasMessage = true;
                            $this->view->messageType = "danger";
                            $this->view->message = "Error";
                        }
                        if ($guest_email) {
                            $this->_redirect('/payment/index/order_id/' . $order_id . '/email/' . $guest_email . '/name/' . $user_name . '/number/' . $number);
                        } else {
                            $this->_redirect('/payment/index/order_id/' . $order_id);
                        }
                    } else {
                        $order_id = $ordersMapper->addNewOrder($orders);

                        $session = new Zend_Session_Namespace("order");

                        $q_total = $total = $session->quantity_total;

                        $total = 0;

                        foreach ($items as $item) {

                            $item_id = $item;
                            $qty = $quantity[$i];
                            $new_price = $price[$i];

                            $item_total = $qty * $new_price;
                            $total += $item_total;
                            $i++;
                            $amount_session = new Zend_Session_Namespace("total_amount");
                            $amount_session->total_amount = $total;


                            if ($order_id) {
                                $orderItems->__set("order_id", $order_id);
                                $orderItems->__set("item_id", $item_id);
                                $orderItems->__set("quantity", $qty);
                                $orderItems->__set("total_price", $item_total);
                                $orderItems->__set("unit_price", $new_price);

                                ($orderItemsMapper->addNewOrderItem($orderItems));
                            }
                        }
                        $orders = $ordersMapper->getOrderById($order_id);

                        if ($total <= 100) {
                            $total_new = $total + 50;
                        } else {
                            $total_new = $total;
                        }

                        $orders->__set("order_total", $total_new);
                        $orders->__set("payment_status", "Unpaid");
                        $service = $orders->__get("service");
                        $d_date = $orders->__get("delivery_date");

                        if ($discount_amount && $revised_amount) {
                            $orders->__set("discount_price", $discount_amount);
                            $orders->__set("revised_price", $revised_amount);
                            
                            $total_new = $revised_amount;
                        }


                        $sms = "Dear " . $fname . ", Your order " . $order_id . " with QTY " . $q_total . " and amount Rs. " . $total_new . " will be ready on " . $d_date . ". Thanks LaundryWala";
                        //$sms = "Dear Divya, Your order 12 with QTY 12 and amount Rs. 200 will be ready on 12. Thanks LaundryWala";
                        $sms_result = $this->_smsNotification($number, $sms);


                        $subject = "Order Details From LaundryWala";

                        $message = "<table style='font-size:12px;'>";
                        $message.= "<tbody>";
                        $message.= "<tr><td>Dear Customer ,<br><br></td></tr>";
                        $message.= "<tr><td>Thank you for your association with LaundryWala.We are pleased to provide you details around your recent transaction with us.<br><br></td></tr>";
                        $message.= "<tr><td><strong>Invoice No :</strong>" . $order_id . "</td></tr>";
                        $message.= "<tr><td><strong>Customer Name :</strong>" . $fname . "</td></tr>";
                        $message.= "<tr><td><strong>Address :</strong>" . $address . "</td></tr>";
                        $message.= "<tr><td><strong>Phone No. :</strong><a>" . $number . "</a></td></tr>";

                        if ($orders) {
                            $email = $orders->__get("user_email");
                            $address = $orders->__get("user_address");
                            $city = $orders->__get("address_city");
                            $state = $orders->__get("address_state");
                            $pincode = $orders->__get("address_pincode");
                            $date = $orders->__get("timestamp");
                            $zendDate = new Zend_Date($date, "dd-MMM-yyyy");
                            $date_new = $zendDate->toString("dd-MMM-yyyy");
                            $number = $orders->__get("user_phn_number");
                            $method = $orders->__get("payment_method");
                            $del_date = $orders->__get("delivery_date");

                            $message.= "<tr><td><strong>Booking Date :</strong>" . $date_new . "<br><strong>Payment Mode:</strong>" . $method . "</td></tr>";
                            $message.= "<tr><td><strong>Total Amount Due Rs.:</strong>" . $total_new . "<br></td></tr>";
                            $message.= "<tr><td><strong>Due Delivery Date :</strong>" . $del_date . "</td></tr>";
                            $message.= "<tr><td><strong>Total Pcs. :</strong>" . $q_total . "</td></tr>";
                            $message.= "<tr style='font-size:12px'>";
                            $message.= "<td colspan='4'>";
                            $message.= "<table style='width:7.9in'>";
                            $message.= "<tbody>";
                            $message.= "<tr>";
                            $message.= "<td style='width:0.5in;font-weight:bold;font-size:12px'>S.No.</td>";
                            $message.= "<td style='width:0.5in;font-weight:bold;font-size:12px'>Qty</td>";
                            $message.= "<td style='width:5.0in;font-weight:bold;font-size:12px' align='left'>Particular's</td>";
                            $message.= "<td style='width:1.0in;font-weight:bold' align='right'></td>";
                            $message.= "<td style='width:1.0in;font-weight:bold;font-size:12px' align='right'>Amount</td>";
                            $message.= "</tr>";
                            $message.= "<tr style='font-size:9px'>";
                            $message.= "<td colspan='5' style='font-weight:bold;border-top-style:solid;border-top-width:thin;border-top-color:#000000'></td>";
                            $message.= "</tr>";

                            $orderItemsMapper = new Application_Model_OrderItemsMapper();
                            $items_new = $orderItemsMapper->getOrderItemByOrderId($orders->__get("order_id"));

                            $i = 1;
                            if ($items_new) {
                                foreach ($items_new as $item) {
                                    $itemsMapper = new Application_Model_ItemsMapper();
                                    $item_name = $itemsMapper->getItemById($item->__get("item_id"));
                                    if ($item_name) {
                                        $name = $item_name->__get("item_name");
                                    } else {
                                        $name = "None";
                                    }
                                    $message.= "<tr style='font-size:12px'>";
                                    $message.= "<td>" . $i++ . "</td>";
                                    $message.= "<td>" . $item->__get("quantity") . "</td>";
                                    $message.= "<td><span style='font-family:arial black;font-size:11px'>" . $name . "</span></td>";
                                    $message.= "<td align='right'></td>";
                                    $message.= "<td align='right'>Rs." . $item->__get("total_price") . "&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;</td>";
                                    $message.= "</tr>";
                                }
                            }
                        }
                        $message.= "<tr style='font-size:10px'><td colspan='5'></td></tr>";
                        $message.= "<tr style='font-size:10px'>";
                        $message.= "<td colspan='3'></td>";
                        $message.= "<td align='right' style='font-size:12px;font-weight:bold'>Subtotal</td>";
                        $message.= "<td align='right' style='font-size:12px;font-weight:bold'>Rs." . $total . "</td>";
                        $message.= "</tr>";
                        if ($total <= 100) {
                            $message.= "<tr style='font-size:10px'>";
                            $message.= "<td colspan='3'></td>";
                            $message.= "<td align='right' style='font-size:12px;font-weight:bold'>Amount after Delivery Charges</td>";
                            $message.= "<td align='right' style='font-size:12px;font-weight:bold'>Rs." . $total_new . "</td>";
                            $message.= "</tr>";
                        } else {
                            $message.= "<tr style='font-size:10px'>";
                            $message.= "<td colspan='3'></td>";
                            $message.= "<td align='right' style='font-size:12px;font-weight:bold'>Amount after Discount</td>";
                            if ($discount_amount) {
                                $message.= "<td align='right' style='font-size:12px;font-weight:bold'>Rs." . $revised_amount . "</td>";
                            } else {
                                $message.= "<td align='right' style='font-size:12px;font-weight:bold'>Rs." . $total_new . "</td>";
                            }

                            $message.= "</tr>";
                        }
                        $message.= "<tr style='font-size:10px'>";
                        $message.= "<td colspan='3'></td>";
                        $message.= "<td align='right' style='font-size:12px;font-weight:bold'><br></td>";
                        $message.= "<td align='right' style='font-size:12px;font-weight:bold'><br></td>";
                        $message.= "</tr>";
                        $message.= "<tr><td nowrap='' colspan='3'>Assuring you of our best service.</td></tr>";
                        $message.= "<tr><td colspan='3'>Warm Regards.</td></tr>";
                        $message.= "<tr><td colspan='3'>LaundryWala</td></tr>";
                        $message.= "</tbody></table></td></tr></tbody></table>";

                        $this->getMailAction($email, $subject, $message);
                        $this->getMailAction("info@laundrywala.co.in", $subject, $message);



                        if ($ordersMapper->updateOrder($orders)) {
                            $this->_helper->redirector('notification', 'checkout');
                            $this->view->hasMessage = true;
                            $this->view->messageType = "success";
                            $this->view->message = "Your Order added sucessfully";
                        } else {
                            $this->view->hasMessage = true;
                            $this->view->messageType = "danger";
                            $this->view->message = "Error";
                        }
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

    protected function _process($data, $treatment = true) {

        // Get our authentication adapter and check credentials

        $adapter = $this->_getAuthAdapter($treatment);

        $adapter->setIdentity($data['user_email']);

        $adapter->setCredential($data['hashed_password']);

        $userAuth = new My_Auth("user");

        $auth = $userAuth;

        $result = $auth->authenticate($adapter);
        if ($result->isValid()) {

            $user = $adapter->getResultRowObject();

            $auth->getStorage()->write($user);

            return true;
        }

        return false;
    }

    protected function _getAuthAdapter($treatment = true) {

        $users = new Application_Model_DbTable_Users;

        $authAdapter = new Zend_Auth_Adapter_DbTable($users->getAdapter());

        if ($treatment) {
            $authAdapter->setTableName('users')
                    ->setIdentityColumn('user_email')
                    ->setCredentialColumn('hashed_password')
                    ->setCredentialTreatment('SHA1(?)');
        } else {
            $authAdapter->setTableName('users')
                    ->setIdentityColumn('user_email')
                    ->setCredentialColumn('hashed_password');
        }


        return $authAdapter;
    }

    public function notificationAction() {
        
    }

    protected function getMailAction($email, $subject, $message) {
        $return = array();
        try {
            $mail = new Zend_Mail();
            $mail->setFrom("info@laundrywala.co.in", "LaundryWala.co.in");
            $mail->addTo($email, 'recipient');
            $mail->setSubject($subject);
            $mail->setBodyHtml($message);
            $return[0] = $mail->send();
            return $return;
        } catch (Exception $e) {
            $return[0] = false;
            $return[1] = $e->getMessage();
            return $return;
        }
    }

    protected function _smsNotification($number, $sms) {

        $number = substr($number, 0, 10);
        $sms = urlencode($sms);
        $url = "http://login.smsgatewayhub.com/smsapi/pushsms.aspx?user=laundrywala&pwd=cleanlaundry&to=91" . $number . "&sid=LAWALA&msg=" . $sms . "&fl=0&gwid=2";
        //echo $url;exit;
        $text = file_get_contents($url);
        return $text;
    }

}
