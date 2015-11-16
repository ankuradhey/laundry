<?php

class IndexController extends Zend_Controller_Action {

    protected $_auth;

    public function init() {
//        $this->_auth = new My_Auth("user");
        $this->db = Zend_Registry::get('db');
//        $this->_helper->layout()->setLayout('master');
        ZendX_JQuery::enableView($this->view);
    }

    public function layoutfunction() {
        $this->_helper->getHelper('layout')->setLayoutPath(APPLICATION_PATH . '/layouts/scripts');
        $this->_helper->getHelper('layout')->setLayout('inner_layout');
        $model = new Users_Model_User();
        return $model;
    }

    public function indexAction() {
        $model = new Users_Model_User();
        
//        $namespace = new Zend_Session_Namespace('userInfo');
//        $namespace->user_id = 17;
//        $namespace->user_fname = 'Ankit';
//        $namespace->user_lname = 'Sharma';
//        $namespace->user_img = $img;
            
        if (!isset($_SESSION))
            {
            $auth = TBS\Auth::getInstance();

            $providers = $auth->getIdentity();

            // Here the response of the providers are registered
            if ($this->_hasParam('provider')) {
                $provider = $this->_getParam('provider');

                switch ($provider) {
                    case "facebook":
                        if ($this->_hasParam('code')) {
                            $adapter = new TBS\Auth\Adapter\Facebook(
                                    $this->_getParam('code'));
                            $result = $auth->authenticate($adapter);
                        }
                        if ($this->_hasParam('error')) {
                            throw new Zend_Controller_Action_Exception('Facebook login failed, response is: ' .
                            $this->_getParam('error'));
                        }
                        break;
                    case "twitter":
                        if ($this->_hasParam('oauth_token')) {
                            $adapter = new TBS\Auth\Adapter\Twitter($_GET);
                            $result = $auth->authenticate($adapter);
                        }
                        break;
                    case "google":

                        if ($this->_hasParam('code')) {
                            $adapter = new TBS\Auth\Adapter\Google(
                                    $this->_getParam('code'));
                            $result = $auth->authenticate($adapter);
                        }
                        if ($this->_hasParam('error')) {
                            throw new Zend_Controller_Action_Exception('Google login failed, response is: ' .
                            $this->_getParam('error'));
                        }
                        break;
                }
                // What to do when invalid
                if (isset($result) && !$result->isValid()) {
                    $auth->clearIdentity($this->_getParam('provider'));
                    throw new Zend_Controller_Action_Exception('Login failed');
                } else {
                    $this->_redirect('/index/connect');
                }
            } else { // Normal login page
                $this->view->googleAuthUrl = TBS\Auth\Adapter\Google::getAuthorizationUrl();
                $this->view->googleAuthUrlOffline = TBS\Auth\Adapter\Google::getAuthorizationUrl(true);
                $this->view->facebookAuthUrl = TBS\Auth\Adapter\Facebook::getAuthorizationUrl();
                // echo "sdd===>".TBS\Auth\Adapter\Facebook::getAuthorizationUrl();exit;
                //  $this->view->twitterAuthUrl = \TBS\Auth\Adapter\Twitter::getAuthorizationUrl();
            }
        }
    }

    public function processcartAction() {
        $cartSession = new Zend_Session_Namespace('laundryCart');
        if ($this->getRequest()->isPost()) {

            $post = $this->getRequest()->getPost();
            //VALIDATION IS TO BE PERFORMED
            if (isset($post['step'])) {
                switch ($post['step']) {
                    case 'services':
                        $model = new Application_Model_ServiceMasterMapper();
                        $itemModel = new Application_Model_ItemsMapper();
                        $services = $model->getAllServiceMasters();

                        foreach ($services as $service) {
                            if (strtolower($service->__get('service_name')) == 'ironing')
                                $ironingServiceId = $service->__get('service_id');
                        }

                        $cartSession->step = '1';
                        $serviceArr = array();
                        $noServiceArr = array();

                        foreach ($post['itemcount'] as $serviceId => $value) {
                            if ($value == 1)
                                $serviceArr[] = $serviceId;
                            else
                                $noServiceArr[] = $serviceId;
                        }

                        //remove all session products which fall under one service which have been removed from session
                        if (count($cartSession->items) && is_array($cartSession->items)) {
                            foreach ($noServiceArr as $noservice) {
                                if (empty($cartSession->items))
                                    break;
                                $noItemsArr = $itemModel->getItemsByServiceIdAndItemId($noservice, implode(",", array_keys($cartSession->items)));
                                $noitems = array();
                                foreach ($noItemsArr as $noitem) {
                                    unset($cartSession->items[$noitem->__get('item_id')]);
                                }
                            }
                        }

                        //check if service is ironing
                        //get ironing 
                        if (count($serviceArr) == 1 && $serviceArr[0] == $ironingServiceId) {
                            $cartSession->delivery_type = 'Fixed Slot';
                        }

                        $cartSession->service = $serviceArr;
                        $this->_redirect('index/items');
                        break;
                    case 'items':
                        $cartSession->step = '2';
                        $post['item'] = array_filter($post['item'], function($value) {
                                    return $value > 0 ? true : false;
                                });
                        
                        //filter price values 
                        foreach($post['itemprice'] as $key=>$value){
                            if(!isset($post['item'][$key]))
                                unset($post['itemprice'][$key]);
                            
                            if(!isset($post['item'][$key])){
                                unset($post['itemservice'][$key]);
                                unset($post['itemcategory'][$key]);
                            }
                        }
                        
                        $cartSession->delivery_type = $post['delivery-type']? : 'Regular';
                        $cartSession->items = $post['item']? : array();
                        $cartSession->itemprice = $post['itemprice']? : 0;
                        $cartSession->itemcategory = $post['itemcategory']? : 0;
                        $cartSession->itemservice = $post['itemservice']? : 0;
                        $this->_redirect('index/address');
                        break;
                    case 'address':
                        $cartSession->step = '3';
                        $cartSession->address = $post['order_address'];
                        $cartSession->address_additional = $post['order_address_additional'];
                        $cartSession->city = $post['order_city'];
                        $cartSession->pincode = $post['order_pincode'];
                        $cartSession->delivery_note = $post['order_delivery_note'];
                        $this->_redirect('index/pickup');
                        break;
                    case 'schedule':
                        $cartSession->step = '4';
                        $cartSession->pickup = $post['pickupTimeSlot'];
                        $cartSession->pickup_date = $post['pickupDate'];
                        $cartSession->delivery = $post['deliveryTimeSlot'];
                        $cartSession->delivery_date = $post['deliveryDate'];
                        $this->_redirect('index/verification');
                        break;
                }
            }
        } else {
            echo "data not posted";
        }
        die;
    }

    public function displayvalue($input) {
        if (is_object($input)) {
            $input = (array) $input;
        }
        $result = array();
        if (is_array($input)) {

            foreach ($input as $key => $value) {

                if (!is_array($value))
                    $result[$key] = $value;

                if (is_array($value) || is_object($value))
                    $this->displayvalue($value);
                else
                    $result[$key] = $value;
            }
        }else {
            $result = $input;
        }
        return $result;
    }
    
    public function faqAction(){
        $this->view->headlineText = 'Frequently Asked Questions';
    }
    
    public function aboutusAction(){
        $this->view->headlineText= 'Our Story';
    }
    
    public function contactusAction(){
        $this->view->headlineText= 'Contact Us';
    }
    
    public function connectAction() {
        $model = $this->layoutfunction();
        $auth = TBS\Auth::getInstance();
        if (!$auth->hasIdentity()) {
            // throw new Zend_Controller_Action_Exception('Not logged in!', 404);
        }
        $data = array();
        //  $data['provider_id']='111327682559075';
        //   $checkuser = $model->checkuserByProviderId($data['provider_id']);
        //    if(count($checkuser)<1){
        foreach ($auth->getIdentity() as $provider) {
            $data['provider_name'] = $provider->getName();
            $data['provider_id'] = $provider->getId();
            $id = $provider->getApi()->getId();
            $profile = $provider->getApi()->getProfile($id);
            
            if($data['provider_name'] == 'facebook')
            $img = $provider->getApi()->getPicture();
//            $data['provider_id']=111327682559075;
            $checkuser = $model->checkuserByProviderId($data['provider_id']);
            if (count($checkuser) < 1) {
                $detail = $this->displayvalue($profile);


                $name = explode(" ", $detail['name']);
                $array['user_email'] = @$detail['email'];
                $array['user_fname'] = $name[0];
                $array['user_lname'] = $name[1];
                $array["user_fb_id"] = $detail['id'];
                $array["last_login"] = date("Y-m-d H:i:s");

                $id = $model->insertanywhere('users', $array);

                if ($id) {
                    $data1['user_id'] = $id;
                    $data1['provider_id'] = $data["provider_id"];
                    $data1['provider_name'] = $data["provider_name"];
                    $model->insertanywhere('user_provider', $data1);
                }
            }
            //    $detailall = $model->getuserById($id);
            // echo "<pre />"; print_r($detailall);exit;
            $checkuser = $checkuser[0];
            $data['user_img'] = $img;
            $namespace = new Zend_Session_Namespace('userInfo');
            $namespace->user_id = $checkuser->user_id;
            $namespace->user_fname = $checkuser->user_fname;
            $namespace->user_lname = $checkuser->user_lname;
            $namespace->user_img = $img;
        }
        //   }
        $this->_redirect('index/orderview');
        $this->view->providers = $auth->getIdentity();
    }

    public function itemsAction() {
        $model = new Application_Model_ServiceMasterMapper();
        $services = $model->getAllServiceMasters();
        $model = new Application_Model_CategoriesMapper();
        $categories = $model->getAllCategories(true);

        $namespace = new Zend_Session_Namespace('userInfo');
        $laundryCart = new Zend_Session_Namespace('laundryCart');
        
        if(isset($namespace->user_id)){
            //check if items are there in session - if not then remove all 
            if (empty($laundryCart->items) || empty($laundryCart->itemprice)) {
                $laundryCart->items = array();
                $laundryCart->itemprice = '';
            }

            //remove delivery type when services are multiple
            if (count($laundryCart->service) > 1 && is_array($laundryCart->service)) {
                $laundryCart->delivery_type = '';
            }

            $this->view->services = $services;
            $this->view->selectedServices = $laundryCart->service? : array();
            $this->view->categories = $categories;
            //list of items from default service selected and category default selected
            //Mens is default category
            $model = new Application_Model_ItemPriceMapper();
            //current selected listing of items
            $items = $model->getItemPriceByServiceIdCatIdDelName($laundryCart->service[0], 'Regular', $categories[0]->__get('category_id'));
            $this->view->items = $items? : array();
            $selectedItems = $this->view->selectedItems = $laundryCart->items;
            $this->view->selectedItemsCount = array_reduce($laundryCart->items, function($item, $carry) {
                        $carry += $item;
                        return $carry;
                    });
            $this->view->deliveryType = $laundryCart->delivery_type? : '';
            $totalPrice = 0;
            foreach($laundryCart->itemprice as $key=>$prices){
                $totalPrice += $prices*$selectedItems[$key];
            }
            $this->view->selectedItemsPrice = $totalPrice;
            $this->view->headlineText = 'SELECT YOUR ITEMS';

            $this->view->user_fname = $namespace->user_fname;
            $this->view->user_lname = $namespace->user_lname;
            $this->view->user_img = $namespace->user_img;
        }else{
            $this->_redirect("");
        }
    }
    
    public function viewordersAction(){
        
    }
    
    public function orderviewAction() {
        $auth = TBS\Auth::getInstance();
        $model = new Application_Model_ServiceMasterMapper();
        $namespace = new Zend_Session_Namespace('userInfo');
//        if(isset($namespace->user_id)){
            $laundryCart = new Zend_Session_Namespace('laundryCart');
            $users = $namespace->data;
            $uid = $users['user_id'];
            $username = $users['fnmae'];
            $category = $model->getAllServiceMasters();
            $this->view->category = $category;
            $this->view->selectedServices = $laundryCart->service? : array();
            $this->view->headlineText = 'Select services you want';
            $this->view->user_fname = $namespace->user_fname;
            $this->view->user_lname = $namespace->user_lname;
            $this->view->user_img = $namespace->user_img;
//        }else{
//            $this->_redirect("");
//        }
    }

    public function addressAction() {
        $laundryCart = new Zend_Session_Namespace('laundryCart');
        $this->view->address = $laundryCart->address;
        $this->view->address_additional = $laundryCart->address_additional;
        $this->view->pincode = $laundryCart->pincode;
        $this->view->city = $laundryCart->city;
        $this->view->delivery_note = $laundryCart->delivery_note;
        $this->view->headlineText = 'Enter Your Full Address';
        
        $namespace = new Zend_Session_Namespace('userInfo');
        $this->view->user_fname = $namespace->user_fname;
        $this->view->user_lname = $namespace->user_lname;
        $this->view->user_img = $namespace->user_img;
    }

    public function pickupAction() {
        $laundryCart = new Zend_Session_Namespace('laundryCart');
        $this->view->deliveryType = $laundryCart->delivery_type;
        $this->view->headlineText = 'Choose Your Collection and Delivery Time';
        
        $namespace = new Zend_Session_Namespace('userInfo');
        $this->view->user_fname = $namespace->user_fname;
        $this->view->user_lname = $namespace->user_lname;
        $this->view->user_img = $namespace->user_img;
    }

    public function verificationAction() {
        $cartSession = new Zend_Session_Namespace('laundryCart');
        $model = new Application_Model_ServiceMasterMapper();
        $serviceArr = array();
        foreach ($cartSession->service as $value) {
            $service = $model->getServiceMasterById((int) $value);
            $serviceArr[] = $service->__get('service_name');
        }
        $fullAddress = $cartSession->address . " " . $cartSession->address_additional . ", " . $cartSession->city . ", " . $cartSession->pincode;
        $this->view->address = $fullAddress;
        $this->view->services = $serviceArr;
        $this->view->pickup = $cartSession->pickup;
        $this->view->delivery = $cartSession->delivery;
        $this->view->pickupDate = $cartSession->pickup_date;
        $this->view->deliveryDate = $cartSession->delivery_date;
        $this->view->headlineText = 'Order Summary';
        
        $namespace = new Zend_Session_Namespace('userInfo');
        $this->view->user_fname = $namespace->user_fname;
        $this->view->user_lname = $namespace->user_lname;
        $this->view->user_img = $namespace->user_img;
    }

    public function orderlistAction(){
        $namespace = new Zend_Session_Namespace('userInfo');
        $this->view->user_fname = $namespace->user_fname;
        $this->view->user_lname = $namespace->user_lname;
        $this->view->user_img = $namespace->user_img;
        if(isset($namespace->user_id)){
            $orderModel = new Application_Model_OrdersMapper();
            $serviceModel = new Application_Model_ServiceMasterMapper();
            $orders = $orderModel->getOrdersByUserId(9);
            
            //get list of services
            $services  = $serviceModel->getAllServiceMasters();
            $servicesArr = array();
            foreach($services as $serviceKey=>$service){
                $servicesArr[$service->__get('service_id')]['service_name'] = $service->__get('service_name');
            }
            
            $ordersArr = array();
            foreach($orders as $key=>$item){
                $ordersArr[$key]['order_id'] = $item->__get('order_id');
                $ordersArr[$key]['order_pickup'] = $item->__get('order_pickup');
                $serviceIds = $item->__get('order_service_type');
                $serviceIds = explode(",",$serviceIds);
                $ordersArr[$key]['order_services'] = array_map(function($val) use ($servicesArr) {
                    return @$servicesArr[$val]['service_name'];
                }, $serviceIds);
                $ordersArr[$key]['order_delivery'] = $item->__get('order_delivery');
            }
            $this->view->orders = $ordersArr;
            $this->view->headlineText = 'My Orders';
        }else{
            $this->_redirect();
        }
    }
    
    public function saveorderAction() {
        //check session
        $laundryCart = new Zend_Session_Namespace('laundryCart');
        $userInfo = new Zend_Session_Namespace('userInfo');
        $model = new Application_Model_OrdersMapper();
        $itemModel = new Application_Model_ItemsMapper();
        $itemPrice = new Application_Model_ItemPriceMapper();
        $orders = new Application_Model_Orders();
        $orderItem = new Application_Model_OrderItems();
        $orderItemModel = new Application_Model_OrderItemsMapper();
        if (isset($laundryCart->service)) {
            $totalPrice = 0;
            foreach($laundryCart->itemprice as $key=>$prices){
                $totalPrice += $prices*$laundryCart->items[$key];
            }
//                && isset($userInfo)){
            $orders->__set('order_user_id', 9);
            $orders->__set('order_first_name', 'Ankit');
            $orders->__set('order_last_name', 'Sharma');
            $orders->__set('order_user_email', 'anki.sharma@gmail.com');
            $orders->__set('order_address', 'AH 70');
//            $orders->__set('order_phone', '123456455');
            $orders->__set('order_city', 'Ghaziabad');
            $orders->__set('order_pincode', '123456');
            $orders->__set('order_delivery_type', $laundryCart->delivery_type);
            $orders->__set('order_pickup', date('Y-m-d',strtotime($laundryCart->pickup_date)));
            $orders->__set('order_delivery', date('Y-m-d',strtotime($laundryCart->delivery_date)));
            $orders->__set('order_delivery_time', $laundryCart->delivery);
            $orders->__set('order_pickup_time', $laundryCart->pickup);
            $orders->__set('order_amount', $totalPrice);
            $orders->__set('order_payment_type', 'cash on delivery');
            $orders->__set('order_payment_status', 'unpaid');
            $orders->__set('order_service_type', implode(',', $laundryCart->service));
            if ($orderId = $model->addNewOrder($orders)) {
                //add products now
                if(isset($laundryCart->items)){
                    foreach($laundryCart->items as $item=>$quantity){
                        //get item details
                        $itemDetails =  $itemModel->getItemById($item);
                        //save product item
                        $orderItem->__set('order_id',$orderId);
                        $orderItem->__set('order_product_name',$itemDetails->__get('item_name'));
                        $orderItem->__set('order_item_id',$itemDetails->__get('item_id'));
                        $orderItem->__set('order_type','service');
                        $orderItem->__set('order_service_name',$laundryCart->itemservice[$item]);
                        $orderItem->__set('order_category_name',$laundryCart->itemcategory[$item]);
                        $orderItem->__set('unit_price',$laundryCart->itemprice[$item]);
                        $orderItem->__set('total_price',$laundryCart->itemprice[$item]*$quantity);
                        $orderItem->__set('quantity',$quantity);
                        $orderItemModel->addNewOrderItem($orderItem);
                    }
                }
                
                $this->view->message = "Admin added successfully";
                $this->view->hasMessage = true;
                $this->view->messageType = "success";
                
                //session destroy
                $laundryCart->unsetAll();
                
                $this->_redirect('');
            } else {
                $this->view->message = "Error occured while adding. Please try again";
                $this->view->hasMessage = true;
                $this->view->messageType = "danger";
            }
        } else {
            exit("error occurred");
        }
    }

    public function loginAction() {
        $request = $this->getRequest();
        $request_type = $request->getParam("request_type", FALSE);
        if ($request_type) {
            if ($request_type == "login") {

                $user_email = $request->getParam("user_email");
                $password = $request->getParam("hashed_password");

                $errors = array();
                if (empty($user_email)) {
                    $errors[] = "Email Should not be empty";
                }
                if (empty($password)) {
                    $errors[] = "Password Should not be empty";
                }
                if (count($errors) == 0) {
                    if ($this->_process($request->getParams())) {

                        $user_id = $this->_auth->getIdentity()->user_id;
                        $this->_helper->redirector('index', 'index');
                    } else {
                        $this->view->hasMessage = true;
                        $this->view->messageType = "danger";
                        $this->view->message = "Invalid Email and Password";
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

    public function registerAction() {

        $request = $this->getRequest();
        $usersMapper = new Application_Model_UsersMapper();
        $userWalletMapper = new Application_Model_UserWalletMapper();
        $userWallet = new Application_Model_UserWallet();
        $request_type = $request->getParam("request_type", FALSE);
        $referred_by = $request->getParam("refer", FALSE);

        if ($referred_by) {
            $user = $usersMapper->getUserByEmail($referred_by);
            $user_id = $user->__get("user_id");
        } else {
            $user_id = 0;
        }
        $this->view->user_id = $user_id;

        $usersMapper = new Application_Model_UsersMapper();
        $users = new Application_Model_Users();

        if ($request_type) {
            if ($request_type == "register") {
                $user_fname = $request->getParam("user_fname");
                $user_lname = $request->getParam("user_lname");
                $user_email = $request->getParam("user_email");
                $password = $request->getParam("hashed_password");
                $cpassword = $request->getParam("cpassword");
                $user_number = $request->getParam("user_number");
                $user_address = $request->getParam("user_address");
                $user_locality = $request->getParam("user_locality");
                $user_landmark = $request->getParam("user_landmark");
                $user_city = $request->getParam("user_city");
                $user_state = $request->getParam("user_state");
                $user_country = $request->getParam("user_country");
                $reference_email = $request->getParam("refer");

                $errors = array();

                if (empty($user_fname)) {
                    $errors[] = "First Name Should Not Be Empty";
                }
                if (empty($user_lname)) {
                    $errors[] = "Last Name Should Not Be Empty";
                }
                if (empty($user_email)) {
                    $errors[] = "Email Should Not Be Empty";
                }
                if (empty($password)) {
                    $errors[] = "Password Should Not Be EMpty";
                }
                if (empty($cpassword)) {
                    $errors[] = "Confirm Password Should Not Be Empty";
                }
                if (empty($user_number)) {
                    $errors[] = "Number Should Not Be Empty";
                }
                if (empty($user_address)) {
                    $errors[] = "Address Should Not Be Empty";
                }
                if (empty($user_city)) {
                    $errors[] = "City Should Not Be Empty";
                }
                if (empty($user_state)) {
                    $errors[] = "State Should Not Be Empty";
                }
                if (empty($user_country)) {
                    $errors[] = "Country Should Not Be Empty";
                }
                if ($password != $cpassword) {
                    $errors[] = "Password Did Not Match";
                }
                if ($user_number < 10 && $user_number > 10) {
                    $errors[] = "Phone Number Should Be of 10 Digits";
                }

                $emailValidator = new Zend_Validate_EmailAddress();
                if (!$emailValidator->isValid($user_email)) {
                    $errors[] = "Email address not valid";
                }
                $options = array(
                    "table" => "users",
                    "field" => "user_email"
                );

                $recordValidation = new Zend_Validate_Db_RecordExists($options);
                if ($recordValidation->isValid($user_email)) {
                    $errors[] = "Email address already in use";
                }
                $hashed_password = sha1($password);
                if (count($errors) == 0) {

                    $users->__set("user_fname", $user_fname);
                    $users->__set("user_lname", $user_lname);
                    $users->__set("user_email", $user_email);
                    $users->__set("user_number", $user_number);
                    $users->__set("hashed_password", $hashed_password);
                    $users->__set("user_address", $user_address);
                    if ($user_locality) {
                        $users->__set("user_locality", $user_locality);
                    } else {
                        $users->__set("user_locality", "");
                    }
                    if ($user_landmark) {
                        $users->__set("user_landmark", $user_landmark);
                    } else {
                        $users->__set("user_landmark", "");
                    }
                    $users->__set("user_city", $user_city);
                    $users->__set("user_state", $user_state);
                    $users->__set("user_country", $user_country);
                    $users->__set("user_fb_id", "");

                    if ($reference_email) {
                        $new_user = $usersMapper->getUserByEmail($reference_email);
                        if ($new_user) {
                            $users->__set("referred_by", $new_user->__get("user_id"));
                        }
                    }


                    $user_id = $usersMapper->addNewUser($users);

                    if ($reference_email) {
                        $new_user = $usersMapper->getUserByEmail($reference_email);
                        if ($new_user) {
                            $reference_by = $new_user->__get("user_id");

                            $userWallet->__set("user_id", $reference_by);
                            $userWallet->__set("entry_type", "CREDIT");
                            $userWallet->__set("entry_amount", "100");

                            $userWalletMapper->addNewUserWallet($userWallet);

                            $userWallet->__set("user_id", $user_id);
                            $userWalletMapper->addNewUserWallet($userWallet);
                        }
                    }

                    $subject = "Hi " . $user_fname . " " . $user_lname . ", Welcome to Laundry Wala";

                    $message = "Dear " . $user_fname . " " . $user_lname . ",<br/><br/>
                            Greetings From LaundryWala.<br/><br/>
                            Thanks for registering with us and choosing us as your preferred laundry service.</strong><br/><br/>
                            We offer affordable and professional laundry service at the convenience of your doorstep.<br/><br/>
                            Do login to our website www.laundrywala.co.in for more details about us.<br/><br/>
                            Look forward to being of service to you.<br/><br/>
                            Best regards,<br/><br/>
                            Customer Care Team<br/><br/>
                            LaundryWala<br/><br/>";


                    $result = $this->_newForgotPasswordNotification($user_email, $subject, $message);
                    $result1 = $this->_newForgotPasswordNotification("info@laundrywala.co.in", $subject, $message);


                    $sms = "Dear " . $user_fname . ", Welcome to LaundryWala. Do login to our website www.laundrywala.co.in for more details. Look forward to being of service to you. Regards, LaundryWala";

                    $sms_result = $this->_smsNotification($user_number, $sms);


                    if ($user_id && $result && $result1) {
                        $this->view->hasMessage = true;
                        $this->view->messageType = "success";
                        $this->view->message = "User has been added successfully";
                    } else {
                        $this->view->hasMessage = true;
                        $this->view->messageType = "danger";
                        $this->view->message = "Error while adding user";
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

    public function forgotPasswordAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $email = $request->getParam("user_email");
            $usersMapper = new Application_Model_UsersMapper();
            if (!$email) {
                $this->view->hasMessage = true;
                $this->view->messageType = "danger";
                $this->view->message = "Enter Your email first";
            } else {
                $user = $usersMapper->getUserByEmail($email);
                //print_r($admin); exit;
                if (!$user) {
                    $this->view->hasMessage = true;
                    $this->view->messageType = "danger";
                    $this->view->message = "Email Address doesn't exists";
                } else {
                    $reset_code = mt_rand() . mt_rand() . mt_rand() . mt_rand() . mt_rand();
                    $user->__set("reset_code", $reset_code);
                    if ($usersMapper->updateUser($user)) {
                        $subject = $user->__get("user_fname") . " " . $user->__get("user_lname") . ", Your password reset link from LaundryWala.com";

                        $message = "Dear " . $user->__get("user_fname") . " " . $user->__get("user_lname") . ",<br/><br/>
                            Please check your login details from LaundryWala.com as below.<br/><br/>
                            Your registered email address : " . $user->__get("user_email") . "<br/><br/>
                            And Your Username : " . $user->__get("user_email") . "<br/><br/>
                            Password reset link <a href='" . $this->view->baseUrl() . "/index/reset-password/code/" . $reset_code . "'>" . $this->view->baseUrl() . "/index/reset-password/code/" . $reset_code . "</a><br/><br/>
                            Thanks and Regards,<br/>
                            For LaundryWala.com<br/>
                            Support Team";

                        $result = $this->_newForgotPasswordNotification($email, $subject, $message);
                        //print_r($result); exit;
                        if ($result) {
                            $this->view->hasMessage = true;
                            $this->view->messageType = "success";
                            $this->view->message = "Password reset link has been mailed to you.";
                        } else {
                            $this->view->hasMessage = true;
                            $this->view->messageType = "success";
                            $this->view->message = "Error while sending password link.";
                        }
                    }
                }
            }
        }
    }

    public function resetPasswordAction() {
        $request = $this->getRequest();
        //echo "inside function";
        $code = $request->getParam("code");
        if ($code) {

            $usersMapper = new Application_Model_UsersMapper();
            $user = $usersMapper->getUserByResetCode($code);
            if (!$user) {

                $this->view->hasMessage = true;
                $this->view->messageType = "danger";
                $this->view->message = "Invalid reset code";
            }

            if ($request->isPost()) {
                $password = $request->getParam("pass");
                $cpassword = $request->getParam("cpass");

                $errors = array();

                if (empty($password)) {
                    $errors[] = "New Password Should not be empty";
                }
                if (empty($cpassword)) {
                    $errors[] = "Confirm Password Should not be empty";
                }

                if ($password != $cpassword) {
                    $this->view->hasMessage = true;
                    $this->view->messageType = "danger";
                    $this->view->message = "Passwords doesn't match, Try again";
                } else {
                    $hashed_password = sha1($password);

                    if (count($errors) == 0) {

                        $user->__set("hashed_password", $hashed_password);
                        $user->__set("reset_code", "");
                        if ($usersMapper->updateUser($user)) {
                            $this->view->hasMessage = true;
                            $this->view->messageType = "success";
                            $this->view->message = "Password reset successful. <a href='" . $this->view->baseUrl() . "/index/login'>Click here</a> to login.";
                        } else {
                            $this->view->hasMessage = true;
                            $this->view->messageType = "danger";
                            $this->view->message = "Error while adding admin";
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

    public function myAccountAction() {
        
    }

    protected function _process($values) {

        // Get our authentication adapter and check credentials
        $adapter = $this->_getAuthAdapter();

        $adapter->setIdentity($values['user_email']); // set identity for username

        $adapter->setCredential($values['hashed_password']);

        $adminAuth = new My_Auth("user"); // session create

        $auth = $adminAuth;

        $result = $auth->authenticate($adapter); //
        //        print_r($result);exit;
        if ($result->isValid()) {

            $admin = $adapter->getResultRowObject(); // takes the complete colum of that row that i want to take

            $auth->getStorage()->write($admin); // through this line we write in session

            return true;
        }

        return false;
    }

    protected function getAuthAdapter() {
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        $_name = 'user';

        $authAdapter->setTableName($_name)
                ->setIdentityColumn('email')
                ->setCredentialColumn('password');


        return $authAdapter;
    }

    protected function _getAuthAdapter() {

        $admins = new Application_Model_DbTable_Admins();

        $authAdapter = new Zend_Auth_Adapter_DbTable($admins->getAdapter());

        $authAdapter->setTableName('users')
                ->setIdentityColumn('user_email')
                ->setCredentialColumn('hashed_password')
                ->setCredentialTreatment('SHA1(?)');

        return $authAdapter;
    }

    public function logoutAction() {
        \TBS\Auth::getInstance()->clearIdentity();
        $auth = Zend_Auth::getInstance()->clearIdentity();
        $namespace = new Zend_Session_Namespace('someaction');
        Zend_Session::destroy();
        //setcookie('userid', null, -1, '/');
        $this->_redirect('index');
    }

    /* public function logoutAction() {
      $adminAuth = new My_Auth("user");
      $adminAuth->clearIdentity();
      $this->_helper->redirector('index', 'index');
      } */

    protected function _newForgotPasswordNotification($email, $subject, $message) {
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
        $text = file_get_contents($url);
        return $text;
    }

}
