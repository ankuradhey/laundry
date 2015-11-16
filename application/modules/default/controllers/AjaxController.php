<?php 

class AjaxController extends Zend_Controller_Action {

    public function init() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function indexAction() {
        
    }

    public function itemslistAction(){
        $this->_helper->layout()->disableLayout();
        $cartSession = new Zend_Session_Namespace('laundryCart');
        $request = $this->getRequest();
        $serviceId = $request->getParam('service');
        $categoryId = $request->getParam('category');
        $deliveryType = $request->getParam('delivery');
        $model = new Application_Model_ItemPriceMapper();
        $serviceModel = new Application_Model_ServiceMasterMapper();
        $categoryModel = new Application_Model_CategoriesMapper();
        $responseArr = array('code'=>404, 'message'=>'required fields serviceid, categoryid or delivery type missing');
        if(!empty($serviceId) && !empty($categoryId) && !empty($deliveryType)){
            
            //get service name
            $service = $serviceModel->getServiceMasterById($serviceId);
            
            //get category name
            $category = $categoryModel->getCategoryById($categoryId);
            
            $items = $model->getItemPriceByServiceIdCatIdDelName($serviceId, $deliveryType, $categoryId);
            $itemsArr = array();
            
            if(!empty($items) && count($items)){
                foreach($items as $itemKey=>$itemVal){

                    $itemsArr[$itemKey] = array(
                                            'item_price_id'=>$itemVal->__get('item_price_id'),
                                            'item_id'=>$itemVal->__get('item_id'),
                                            'price'=>$itemVal->__get('price'),
                                            'price'=>$itemVal->__get('price'),
                                            'item_name'=>$itemVal->__get('item_name'),
                                            'quantity' => 0,
                                            'item_service' => $serviceId,
                                            'item_service_name' => $service->__get('service_name'),
                                            'item_category' => $categoryId,
                                            'item_category_name' => $category->__get('category_name')
                                          ); 
    //                $isCart = in_array($itemVal->__get('item_id'), $cartSession->items);
                    $isCart = array_key_exists($itemVal->__get('item_price_id'), $cartSession->items);
                    if($isCart && isset($cartSession->items[$itemVal->__get('item_price_id')])){
                        $itemsArr[$itemKey]['quantity'] = $cartSession->items[$itemVal->__get('item_price_id')];
                    }
                }
            }
            $responseArr['code'] = '200';
            $responseArr['message'] = 'success';
            $responseArr['data'] = (array)$itemsArr;
        }
        
        echo json_encode($responseArr, true);
    }
    
    public function loginAction() {

        $request = $this->getRequest();
        $errors = array();
        $email = $request->getParam("user_email");
        $pass = $request->getParam("password");

        if (empty($email)) {
            $errors[] = "Email should not be empty";
        }
        if (empty($pass)) {
            $errors[] = "Password field should not be empty";
        }

        if (count($errors) == 0) {
            $data = array(
                'user_email' => $email,
                'hashed_password' => $pass,
            );
            if ($this->_process($data)) {
                $meta = array(
                    "code" => "200",
                    "message" => "Success"
                );
                $arr = array(
                    "meta" => $meta,
                    "data" => $data
                );
                //$this->_helper->redirector('checkout', 'checkout');
            } else {
                $meta = array(
                    "code" => "404",
                    "message" => "Invalid Username or Password"
                );
                $arr = array(
                    "meta" => $meta,
                );
            }
        } else {
            $errorString = implode(",", $errors);
            $meta = array(
                "code" => "400",
                "message" => $errorString
            );

            $arr = array(
                "meta" => $meta,
            );
        }
        $json = json_encode($arr);
        echo $json;
    }

    public function addAddressesAction() {

        $request = $this->getRequest();
        $addressMapper = new Application_Model_SavedAddressesMapper();
        $del_address = new Application_Model_SavedAddresses();

        $errors = array();

        $user_id = $request->getParam("user_id");
        $user_name = $request->getParam("username");
        $pincode = $request->getParam("pincode");
        $landmark = $request->getParam("landmark");
        $phone = $request->getParam("phone");
        $address = $request->getParam("address");
        $terms = $request->getParam("terms");
        $city = $request->getParam("city");
        $state = $request->getParam("state");
        $guest_email = $request->getParam("guest_email");

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



        if ($user_id) {
            if (count($errors) == 0) {

                $usersMapper = new Application_Model_UsersMapper();
                $user = $usersMapper->getUserById($user_id);


                $del_address->__set("user_id", $user_id);
                $del_address->__set("address_pincode", $pincode);
                $del_address->__set("saved_address", $address);
                $del_address->__set("address_landmark", $landmark);
                $del_address->__set("contact_person_phn", $phone);
                $del_address->__set("contact_person_name", $user_name);
                $del_address->__set("address_city", $city);
                $del_address->__set("address_state", $state);
                $del_address->__set("address_street", "");
                $del_address->__set("address_locality", "");
                $del_address->__set("address_country", "India");

                $data = array(
                    "address" => $address,
                    "landmark" => $landmark,
                    "city" => $city,
                    "state" => $state,
                    "pincode" => $pincode
                );

                if ($addressMapper->addNewSavedAddress($del_address)) {

                    $meta = array(
                        "code" => "200",
                        "message" => "Success"
                    );

                    $arr = array(
                        "meta" => $meta,
                        "data" => $data
                    );
                } else {
                    $meta = array(
                        "code" => "501",
                        "message" => "Error While Adding Address"
                    );

                    $arr = array(
                        "meta" => $meta,
                    );
                }
                $add_session = new Zend_Session_Namespace("address");
                $add_session->username = $user_name;
                $add_session->pincode = $pincode;
                $add_session->landmark = $landmark;
                $add_session->city = $city;
                $add_session->address = $address;
                $add_session->state = $state;
                $add_session->phone = $phone;
            } else {
                $errorString = implode(",", $errors);
                $meta = array(
                    "code" => "400",
                    "message" => $errorString
                );

                $arr = array(
                    "meta" => $meta,
                );
            }
        } else {

            $add_session = new Zend_Session_Namespace("address");
            $add_session->username = $user_name;
            $add_session->pincode = $pincode;
            $add_session->landmark = $landmark;
            $add_session->city = $city;
            $add_session->address = $address;
            $add_session->state = $state;
            $add_session->phone = $phone;
            $add_session->email = $guest_email;

            $data = array(
                "address" => $address,
                "landmark" => $landmark,
                "city" => $city,
                "state" => $state,
                "pincode" => $pincode
            );


            if (isset($add_session)) {
                $meta = array(
                    "code" => "200",
                    "message" => 'Success'
                );

                $arr = array(
                    "meta" => $meta,
                    "data" => $data
                );
            } else {
                $meta = array(
                    "code" => "401",
                    "message" => "Error"
                );

                $arr = array(
                    "meta" => $meta,
                );
            }
        }

        $json = json_encode($arr);
        echo $json;
    }

    public function savedAddressesAction() {

        $request = $this->getRequest();


        $pincode = $request->getParam("pincode");
        $landmark = $request->getParam("landmark");
        $phone = $request->getParam("phone");
        $address = $request->getParam("address");
        $city = $request->getParam("city");
        $state = $request->getParam("state");


        $add_session = new Zend_Session_Namespace("address");
        $add_session->pincode = $pincode;
        $add_session->landmark = $landmark;
        $add_session->city = $city;
        $add_session->address = $address;
        $add_session->state = $state;
        $add_session->phone = $phone;


        if (isset($add_session)) {

            $meta = array(
                "code" => "200",
                "message" => "Success"
            );

            $arr = array(
                "meta" => $meta,
            );
        } else {
            $meta = array(
                "code" => "501",
                "message" => "Error"
            );

            $arr = array(
                "meta" => $meta,
            );
        }

        $json = json_encode($arr);
        echo $json;
    }

    public function dateSessionAction() {

        $request = $this->getRequest();


        $pick_date = $request->getParam("date");
        $pick_time = $request->getParam("time");
        $del_date = $request->getParam("del_date");
        $del_time = $request->getParam("del_time");
        $service_id = $request->getParam("service_name");
        $del_name = $request->getParam("del_name");


        $date_session = new Zend_Session_Namespace("date");
        $date_session->pick_date = $pick_date;
        $date_session->pick_time = $pick_time;
        $date_session->del_date = $del_date;
        $date_session->del_time = $del_time;
        $date_session->service_id = $service_id;
        $date_session->del_name = $del_name;

        if (isset($date_session)) {

            $meta = array(
                "code" => "200",
                "message" => "Success"
            );

            $arr = array(
                "meta" => $meta,
            );
        } else {
            $meta = array(
                "code" => "501",
                "message" => "Error"
            );

            $arr = array(
                "meta" => $meta,
            );
        }

        $json = json_encode($arr);
        echo $json;
    }

    public function couponAction() {

        $request = $this->getRequest();

        $couponsMapper = new Application_Model_CouponsMapper();
        $code = $request->getParam("coupon");
        $total_price = $request->getParam("total");


        $coupon = $couponsMapper->getCouponByCouponCode($code);

        if (!$coupon) {
            $meta = array(
                "code" => "404",
                "message" => "Coupon Not Found"
            );

            $arr = array(
                "meta" => $meta,
            );
        } elseif ($total_price < 200) {
            $meta = array(
                "code" => "400",
                "message" => "Coupon Not Applicable"
            );

            $arr = array(
                "meta" => $meta,
            );
        } else {
            $disc_price = 0;
            $type = $coupon->__get("coupon_type");

            switch ($type) {
                case "flat":
                    $disc_price = $coupon->__get("coupon_value");
                    break;
                case "percentage":
                    $disc_price = ($coupon->__get("coupon_value") / 100) * $total_price;
                    break;
                default:
                    $disc_price = 0;
                    break;
            }
            $revised_price = $total_price - $disc_price;

            if ($revised_price < 0) {
                $revised_price = 0;
            }

            $data = array(
                "discount_price" => $disc_price,
                "revised_price" => $revised_price
            );

            $meta = array(
                "code" => "200",
                "message" => "Success"
            );

            $arr = array(
                "meta" => $meta,
                "data" => $data
            );
        }

        $json = json_encode($arr);
        echo $json;
    }

    public function sendInvitationAction() {

        $request = $this->getRequest();
        $user_email = $request->getParam("user_email");
        $emails = $request->getParam("emails");

        $usersMapper = new Application_Model_UsersMapper();
        $user = $usersMapper->getUserByEmail($user_email);
        if ($user) {
            $fname = $user->__get("user_fname");
        } else {
            $fname = "";
        }

        $errors = array();

        if (empty($emails)) {
            $errors[] = "Please Enter Emails";
        }
        if (count($errors) == 0) {
            $new_emails = explode(",", $emails);

            foreach ($new_emails as $email) {

                $subject = "Invitation Link from LaundryWala";

                $message = "<table width='100%' border='0' cellspacing='0' cellpadding='0' background='" . $this->view->baseUrl() . "front/images/bg.jpg'><tbody>";
                $message.= "<tr> <td align='center'> <table border='0' cellpadding='0' cellspacing='0' width='80%'><tbody>";
                $message.= "<tr> <td rowspan='2' height='170px' valign='top'><img id='3892828000000097003_imgsrc_url_0' alt='Get 20% off on your first bill' src='" . $this->view->baseUrl() . "/front/images/discImg.png'></td>";
                $message.= "<td height='44px' width='100%' style='background-repeat: repeat-x'></td> <td><img id='3892828000000097003_imgsrc_url_1' src='" . $this->view->baseUrl() . "/front/images/rodCorner.png'></td> </tr>";
                $message.= "<tr> <td> <p style='margin:0;color:#000!important'>Dear Customer,</p> <p style='color:#000!important'>Your friend <i>" . $fname . "</i> has recommended you to our laundry services with an exciting 20 % off on your first bill.</p> <p style='color:#000!important'>To know more about our laundry services, visit our website <a href='" . $this->view->baseUrl() . "/index/register/refer/" . $user_email . "'>Click Here</a>&nbsp;or call us on <a>+91&nbsp;</a>9953-1800-71</p> </td> </tr> <tr> <td></td> </tr>";
                $message.= "<tr> <td align='left' style='background-repeat: no-repeat'></td> <td align='center' style='background-repeat: repeat-x; width: 100%'>&nbsp;</td> <td align='right' style='background-repeat: no-repeat'>&nbsp;</td> </tr>";
                $message.= "<tr> <td align='left'><a href='http://laundrywala.co.in' target='_blank'><img width='180' id='3892828000000097003_imgsrc_url_2' alt='Laundry Wala' src='" . $this->view->baseUrl() . "/front/images/mail-logo.png'></a></td>";
                $message.= "<td align='center'><img id='3892828000000097003_imgsrc_url_3' alt='Laundry Wala Features' src='" . $this->view->baseUrl() . "/front/images/chamakFeatures.png'>";
                $message.= "</td> </tr> </tbody></table>";
                $message.= "</td> </tr> </tbody></table>";

                $result = $this->getMailAction($email, $subject, $message);

                if ($result) {
                    $meta = array(
                        "code" => "200",
                        "message" => "Success"
                    );

                    $arr = array(
                        "meta" => $meta,
                    );
                } else {
                    $meta = array(
                        "code" => "501",
                        "message" => "Error While Sendind Mail"
                    );

                    $arr = array(
                        "meta" => $meta,
                    );
                }
            }
        } else {
            $errorString = implode(",", $errors);
            $meta = array(
                "code" => "400",
                "message" => $errorString
            );

            $arr = array(
                "meta" => $meta,
            );
        }
        $json = json_encode($arr);
        echo $json;
    }

    public function contactFormAction() {

        $request = $this->getRequest();
        $email = $request->getParam("email");
        $name = $request->getParam("name");
        $new_message = $request->getParam("message");

        $errors = array();

        if (empty($name)) {
            $errors[] = "Please Enter Your Name";
        }
        if (empty($email)) {
            $errors[] = "Please Enter Your Email";
        }
        if (empty($new_message)) {
            $errors[] = "Please Enter Your Message";
        }
        if (count($errors) == 0) {
            $subject = "Greetings from LaundryWala";
            $message = "Thank You for visiting LaundryWala.Your Message has been successfully recieved";

            $result = $this->getMailAction($email, $subject, $message);

            if ($result) {
                $meta = array(
                    "code" => 200,
                    "message" => 'Success'
                );
                $arr = array(
                    "meta" => $meta,
                );
            } else {
                $meta = array(
                    "code" => "501",
                    "message" => "Error While Sending Mail"
                );

                $arr = array(
                    "meta" => $meta,
                );
            }
        } else {
            $errorString = "<ul class='error-list'>";
            foreach ($errors as $error) {
                $errorString .= "<li>" . $error . "</li>";
            }
            $errorString .= "</ul>";
            $meta = array(
                "code" => "400",
                "message" => $errorString
            );

            $arr = array(
                "meta" => $meta,
            );
        }
        $json = json_encode($arr);
        echo $json;
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

    public function logoutAction() {
        $auth = new My_Auth("user");
        $result = $auth->clearIdentity();
        $this->_helper->redirector('index', 'index');
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }

}
