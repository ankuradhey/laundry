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



    /* FUNCTION :: Import Orders */
    public function importordersAction(){
	 
	   $request = $this->getRequest();
            if ($request->isPost()) {
  
	
	       $config = Zend_Controller_Front::getInstance()->getParam('bootstrap');
		   $resoucres = $config->getOption('resources');
		   
	   if($_FILES['order_import']['name']==''){
	      $this->view->hasMessage = true;
          $this->view->messageType = "danger";
         $this->view->message = "Error occured while importing. Please try again.";
	   }
	   if ($_FILES['order_import']['name'])
            {
				
	          $connect = @mysql_connect($resoucres['db']['params']['host'],$resoucres['db']['params']['username'],$resoucres['db']['params']['password']);
			  $cid =mysql_select_db($resoucres['db']['params']['dbname'],$connect);
	
	          $csv_file = $_FILES['order_import']['tmp_name']; 
	          //prd($_FILES);
               if (($handle = fopen($csv_file, "r")) !== FALSE) {
				   fgetcsv($handle);   
				   while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					    $num = count($data);
						for ($c=0; $c < $num; $c++) {
						  $col[$c] = $data[$c];
						}
						
              
				$query = "INSERT INTO orders(order_user_id,order_delivery_type,order_first_name,order_last_name,order_user_email,order_address,
				order_address_additional,order_city,order_mobile_number,order_pincode,order_delivery_note,order_pickup,order_delivery,order_pickup_time,
				order_delivery_time,order_amount,order_coupon_dis,order_payment_type,order_type,order_service_type,order_payment_status,
				delivery_charge,service_tax,order_status,order_delivery_boy,order_pickup_boy,order_added_time,order_modified_time) 
				          VALUES('".$col[0]."','".$col[1]."','".$col[2]."','".$col[3]."','".$col[4]."','".$col[5]."',
				          '".$col[6]."','".$col[7]."','".$col[8]."','".$col[9]."','".$col[10]."','".$col[11]."','".$col[12]."','".$col[13]."',
				          '".$col[14]."','".$col[15]."','".$col[17]."','".$col[18]."','".$col[19]."','".$col[20]."','".$col[21]."',
				          '".$col[22]."','".$col[23]."','".$col[24]."','".$col[25]."','".$col[25]."','".$col[26]."','".$col[26]."')";


				$s     = mysql_query($query, $connect );
				 }
					fclose($handle);
				}
               
				$this->view->hasMessage = true;
                            $this->view->messageType = "success";
                            $this->view->message = "File data successfully imported to database!!.";
				$this->_redirect('admin/orders/summary');
				mysql_close($connect);
		}		
       
   }

	}
    /* IMPORT ORDER*/
    
    public function exportordersAction(){
		
		   $config = Zend_Controller_Front::getInstance()->getParam('bootstrap');
		   $resoucres = $config->getOption('resources');
		   $conn = @mysql_connect($resoucres['db']['params']['host'],$resoucres['db']['params']['username'],$resoucres['db']['params']['password']);
			mysql_select_db($resoucres['db']['params']['dbname'],$conn);

			$filename = date('Y-m-d')."_laundry_orders.csv";
			$fp = fopen('php://output', 'w');

			$query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='".$resoucres['db']['params']['dbname']."' AND TABLE_NAME='orders'";
			$result = mysql_query($query);
			while ($row = mysql_fetch_row($result)) {
				$header[] = $row[0];
			}	
			header('Content-type: application/csv');
			header('Content-Disposition: attachment; filename='.$filename);
			fputcsv($fp, $header);

			$num_column = count($header);		
			$query = "SELECT * FROM orders";
			$result = mysql_query($query);
			while($row = mysql_fetch_row($result)) {
				fputcsv($fp, $row);
			}
			exit;
	
	}
    
    public function indexAction() {
        try {
            $order_form = new Application_Form_OrdersForm();
            $this->view->form = $order_form;

            $ordersMapper = new Application_Model_OrdersMapper();
            $order = new Application_Model_Orders();
            $model = new Application_Model_OrdersMapper();
            $orderItem = new Application_Model_OrderItems();
            $request = $this->getRequest();
            if ($request->isPost()) {
                $request_type = $request->getParam("request_type", false);
                if ($request_type) {

                    if ($request_type == "add") {
                        $params = $request->getParams();
//                        echo '<pre>'; print_r(get_class_methods($order_form)); exit('Macro die');
//                        echo '<pre>'; print_r($order_form->getErrors($params)); exit('Macro die');
                        if ($order_form->isValid($params)) {
                            foreach ($params as $param => $value) {
                                if ($param == 'order_service_type') {
                                    $value = implode(',', $value);
                                }
                                $order->__set($param, $value);
                            }
                            $post = $params;
//                            if ($orderId = $ordersMapper->addNewOrder($order)) 
                            {
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
                                }else{
                                    //get delivery type
                                    $deliveryTypeMaster = new Application_Model_DeliveryTypeMasterMapper();
                                    $deliveryTypes = $deliveryTypeMaster->getAllDeliveryTypeMaster();
                                    $deliveryArr = array();
                                    foreach ($deliveryTypes as $del) {
                                        $deliveryArr[$del->__get('delivery_type_name')] = $del->__get('delivery_type_id');
                                    }
                                    if (!isset($post['order_delivery_type']))
                                        $deliveryType = 'Regular';
                                    else
                                        $deliveryType = $post['order_delivery_type'];
                                    
                                    $totalPrice = 0;
                                    foreach ($post['item'] as $key => $qty) {
                                        $totalPrice += $post['itemprice'][$key] * $qty;
                                    }
                                    
                                    if($totalPrice < 200){
                                        $deliveryCharge = 50;
                                    }else{
                                        $deliveryCharge = 0;
                                    }
                                    $serviceTax = 14*($totalPrice)/100;
                                    $orders = new Application_Model_Orders();
                                    $orders->__set('order_user_id', $post['order_user_id']);
                                    $orders->__set('order_first_name', $post['order_first_name']);
                                    $orders->__set('order_last_name', $post['order_last_name']);
                                    $orders->__set('order_address', $post['order_address']);
                                    $orders->__set('order_city', $post['order_city']);
                                    $orders->__set('order_delivery_type', $deliveryArr[$deliveryType]);
                                    $orders->__set('order_pickup', date('Y-m-d', strtotime($post['order_pickup'])));
                                    $orders->__set('order_delivery', date('Y-m-d', strtotime($post['order_delivery'])));
                                    $orders->__set('order_delivery_time', $post['order_delivery_time']);
                                    $orders->__set('order_pickup_time', $post['order_pickup_time']);
                                    $orders->__set('order_amount', $totalPrice);
                                    $orders->__set('order_payment_type', 'cash on delivery');
                                    $orders->__set('delivery_charge', $deliveryCharge);
                                    $orders->__set('service_tax', $serviceTax);
                                    $orders->__set('order_payment_status', 'unpaid');
                                    $orders->__set('order_service_type', implode(',', $post['order_service_type']));
                                    $orders->__set('order_type', 'service');
                                    $orders->__set('order_mobile_number', $post['order_mobile_number']);
                                    //TO DO - delivery boy allotted
                                    $orders->__set('order_pickup_boy', '1');
                                    $orders->__set('order_delivery_boy', '1');
                                    
                                    
                                    $itemModel = new Application_Model_ItemsMapper();
                                    $itemPrice = new Application_Model_ItemPriceMapper();
                                    $orderItemModel = new Application_Model_OrderItemsMapper();
                                    if ($orderId = $model->addNewOrder($orders)) {
                                        //add products now
                                        if (isset($post['item'])) {
                                            foreach ($post['item'] as $item => $quantity) {
                                                if((int)$quantity < 1)
                                                    continue;
                                                //get item details
                                                $itemDetails = $itemPrice->getItemPriceById($item);
                                                $itemDetails = $itemModel->getItemById($itemDetails->__get('item_id'));
                                                //save product item
                                                $orderItem->__set('order_id', $orderId);
                                                $orderItem->__set('order_product_name', $itemDetails->__get('item_name'));
                                                $orderItem->__set('order_item_id', $itemDetails->__get('item_id'));
                                                $orderItem->__set('order_type', 'service');
                                                $orderItem->__set('order_service_name', $post['itemservice'][$item]);
                                                $orderItem->__set('order_category_name', $post['itemcategory'][$item]);
                                                $orderItem->__set('unit_price', $post['itemprice'][$item]);
                                                $orderItem->__set('total_price', $post['itemprice'][$item] * $post['item'][$item]);
                                                $orderItem->__set('quantity', $post['item'][$item]);
                                                $orderItemModel->addNewOrderItem($orderItem);
                                            }
                                        }

                                        $this->view->message = "Order successfully completed";
                                        $this->view->hasMessage = true;
                                        $this->view->messageType = "success";

                                        $this->_redirect('admin/orders');
                                    } else {
                                        $this->view->message = "Error occured while adding. Please try again";
                                        $this->view->hasMessage = true;
                                        $this->view->messageType = "danger";
                                    }
                                }

                                $this->view->message = "Order added successfully";
                                $this->view->hasMessage = true;
                                $this->view->messageType = "success";
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
        pr($orderItems);
        prd($orderDetail);
        $this->view->orderDetail = $orderDetail;
        $this->view->orderItems = $orderItems;
    }

    public function printreceiptAction() {

       
        $this->_helper->layout->disableLayout();
        error_reporting(0);
        $orderId = $this->_getParam("order");
       
        $ordersMapper = new Application_Model_OrdersMapper();

        $orderDetail = $ordersMapper->getOrderById($orderId);

        $orderItemsMapper = new Application_Model_OrderItemsMapper();
        $orderItems = $orderItemsMapper->getOrderItemByOrderId($orderId);

        prd($orderDetail);
        
        if (empty($orderDetail)) {
            echo "Order Not Found";
            die;
        }

			$arr = array('one','two','three','four','five');
			$html = "<div style='background:red;color:black;'>";
			foreach($arr as $value){
			$html .= $value.'<br />';
    }
			$html .= "</div>"; 
           echo $html;

           
         prd($orderDetail);
        ///$this->view->orderDetail = $orderDetail;
       // $this->view->orderItems = $orderItems;
        
        exit();
}
    
    public function printorderAction() {

       
        $this->_helper->layout->disableLayout();
        error_reporting(0);
        $orderId = $this->_getParam("order");
        $i='1';
       
        $ordersMapper = new Application_Model_OrdersMapper();

        $orderDetail = $ordersMapper->getOrderById($orderId);
       
        $orderItemsMapper = new Application_Model_OrderItemsMapper();
        $orderItems = $orderItemsMapper->getOrderItemByOrderId($orderId);
        
        if (empty($orderDetail)) {
            echo "Order Not Found";
            die;
        }
         
         //echo $orderItems[0]->order_service_name;
         
         $code = $orderId.'-0000-'.$orderDetail->order_user_id;
         $bc = new Barcode39($code);

        $bc->draw("public/barcode/barcode_".$orderId.'_'.$orderDetail->order_user_id.".gif");
         
         $barcode = '<img src="../../public/barcode/barcode_'.$orderId.'_'.$orderDetail->order_user_id.'.gif">';
       
         $print = '';
         $print.='<div id="divToPrint" ><div style="width:290px;background-color:#ffffff;border:1px solid #ccc;padding:4px;font-size:12px;"><h2 style="text-align:center;" >Laundrywala<hr></h2><table  cellspacing="4"  cellpadding="4" style="padding:4px; font-size:12px;"><tr><td> Address :</td><td>A-109Q,2nd Flr,<br>Sec-80 </td></tr><tr><td>State :</td><td>Noida-201305</td></tr><tr><td>Contact :</td><td>9953180071</td></tr><tr><td>Mail :</td><td>info@laundrywala.co.in </td></tr><tr><td>TIN No :</td><td>NA </td></tr><tr><td>Service Tax No :</td><td>NA </td></tr><tr><td>CIN No: </td><td>NA</td></tr><tr></table>';  
         $print.='<hr><h4 style="text-align:center;">Customer Details</h4><table  cellspacing="4"  cellpadding="4" style="padding:4px; font-size:12px;">';
         $print.='<tr><td>Name :</td><td>'.$orderDetail->order_first_name.' '.$orderDetail->order_last_name.'</td></tr><tr><td>Address :</td><td>'.$orderDetail->order_address.'</td></tr><tr><td>City/State</td><td>'.$orderDetail->order_city.'</td></tr><tr><td>Contact No. </td><td>'.$orderDetail->order_mobile_number.'</td></tr><tr></table><hr>';
         $print.='<h4 style="text-align:center;">Order Details</h4><table border="1" cellpadding="1" cellspacing="0"  style=" font-size:10px;padding:2px; width:100%;" > <tr style="border:1px solid #000"><td>SN</td><td>Service</td><td>Product</td><td>QTY</td><td>Amount</td><td>Total</td></tr>';
	    foreach($orderItems as $orderItems){
	    $print.='<tr style="border:1px solid #000"><td>'.$i.'</td><td>'.$orderItems->order_service_name.'</td><td>'.$orderItems->order_product_name.'</td><td>'.$orderItems->quantity.'</td><td>'.$orderItems->unit_price.'</td><td>'.$orderItems->total_price.'</td></tr>';$i++;}
	    $print.='<tr ><td></td><td></td><td></td><td></td><td>Delivery Charges</td><td>'.$orderDetail->delivery_charge.'</td>';
	    $print.='</tr><tr ><td></td><td></td><td></td><td></td><td>Service Tax(15%)</td><td>'.$orderDetail->service_tax.'</td></tr>';
	     $print.='<tr style="border:1px solid #000" ><td></td><td></td><td></td><td></td><td>Total</td><td>'.$orderDetail->order_amount.'</td></tr></table>';
	     $print.='<div style="text-align:center;" >'.$barcode.'</div></div></div>';

        $fp = trim($print); 
		
		echo $fp;




		
        
        exit();
    }
    
    public function amitAction(){
	 


		$bc = new Barcode39("123-ABC");

        $bc->draw("public/barcode.gif");
	 
	 
	}
    

}
