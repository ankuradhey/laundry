<?php 

class PaymentController extends Zend_Controller_Action {

    protected $_auth;

    public function init() {
//        $this->_auth = new My_Auth("user");
        $this->_auth = $auth = TBS\Auth::getInstance();

    }

    public function indexAction() {
        try {
            $usersMapper = new Application_Model_UsersMapper();
            $transaction_type = "";
            $id = "";
            $amount = 0;
            $request = $this->getRequest();
            $package_id = $request->getParam("package_id");
            $type = $request->getParam("transaction_type");
            $order_id = $request->getParam("order_id");


            if ($order_id) {
                $transaction_type = "Online";
                $id = $order_id;
                $ordersMapper = new Application_Model_OrdersMapper();
                $order = $ordersMapper->getOrderById($order_id);
                $amount = $order->__get("order_amount");
            }
            $namespace = new Zend_Session_Namespace('userInfo');
//            if ($this->_auth->hasIdentity()) {
            if (!empty($namespace->user_id)) {
//                $user_id = $this->_auth->getIdentity()->user_id;
                $user_id = $namespace->user_id;
                $user = $usersMapper->getUserById($user_id);

                if ($package_id) {
                    $transaction_type = "Package";
                    $packagesMapper = new Application_Model_PackagesMapper();
                    $package = $packagesMapper->getPackageById($package_id);
                    //$amount = $package->__get("package_price");
                    $this->view->package = $package;
                    $this->view->transaction_type = $transaction_type;
                }

                $fname = $user->__get("user_fname");
                $lname = $user->__get("user_lname");
                $email = $user->__get("user_email");
                $number = $user->__get("user_number");
            } else {
                    $this->_redirect("/index/login/?redirect_url=".$ref);
            }
            $merchant_key = "jqsdG2";
            
            //test key
            $merchant_key = "gtKFFx";
            $salt = "dwf1Ltip";
            
            //test salt
            $salt = "eCwWELxi";
            $baseUrl = "https://secure.payu.in";
            $baseUrl = "https://test.payu.in";

            $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
            $service_provider = "payu_paisa";
            //test
            $service_provider = "";
            
            
            $surl = BASE_URL . "index/orderlist?SUCC=succ";
            $furl = BASE_URL . "index/orderlist?SUCC=err";





            $posted = array();

            $posted['key'] = $merchant_key;
            $posted['txnid'] = $txnid;
            $posted['amount'] = $amount;
            $posted['firstname'] = $fname;
            $posted['email'] = $email;
            $posted['phone'] = $number;
            $posted['productinfo'] = "transaction_type:" . $transaction_type . "|user_id:" . $user_id . "|id:" . $id."|package:".$packageId; //$product_info;
            $posted['surl'] = $surl;
            $posted['furl'] = $furl;
            $posted['service_provider'] = $service_provider;
            //print_r($posted);exit;
            $hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
            $hashVarsSeq = explode('|', $hashSequence);
            $hash_string = '';
            foreach ($hashVarsSeq as $hash_var) {
                $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
                $hash_string .= '|';
            }
            $hash_string .= $salt;
            $hash = strtolower(hash('sha512', $hash_string));
            $action = $baseUrl . '/_payment';
            
            $this->view->posted = $posted;
            $this->view->hash = $hash;
            $this->view->action = $action;
            $this->view->authorized = TRUE;
        } catch (Exception $ex) {
            $this->view->authorized = FALSE;
            $this->view->message = $ex->getMessage();
        }
    }

    public function successAction() {
        try {

//            $userWalletMapper = new Application_Model_UserWalletMapper();
//            $userWallet = new Application_Model_UserWallet();
//            $userPackagesMapper = new Application_Model_UserPackagesMapper();
//            $userPackages = new Application_Model_UserPackages();
            $userTrackMapper = new Application_Model_UserTrackMapper();
            $userTrack = new Application_Model_UserTrack();
            
            $ordersMapper = new Application_Model_OrdersMapper();
//            $packagesMapper = new Application_Model_PackagesMapper();
            $userTransactionsMapper = new Application_Model_UserTransactionsMapper();
            $userTransactions = new Application_Model_UserTransactions();

            $request = $this->getRequest();
            $status = $request->getParam("status");
            $fname = $request->getParam("firstname");
            $amount = $request->getParam("amount");
            $txnid = $request->getParam("txnid");
            $posted_hash = $request->getParam("hash");
            $product_info = $request->getParam("productinfo");
            $key = $request->getParam("key");
            $email = $request->getParam("email");
            $code = $request->getParam("bankcode");

            $salt = "dwf1Ltip";
            
            //test salt
            $salt = "GQs7yium";
            
            $retHashSeq = $salt . '|' . $status . '|||||||||||' . $email . '|' . $fname . '|' . $product_info . '|' . $amount . '|' . $txnid . '|' . $key;
            $hash = hash("sha512", $retHashSeq);

            $infos = explode("|", $product_info);
            $transaction_type = explode(":", $infos[0]);
            $transaction_type = $transaction_type[1];

            //echo $transaction_type;exit;


            $user_id = explode(":", $infos[1]);
            $user_id = $user_id[1];

            $id = explode(":", $infos[2]);
            $id = $id[1];

            $package_id = explode(":",$infos[3]);
            $package_id = $package_id[1];
            
            if ($user_id != 0) {
                $userTransactions->__set("trnx_user_id", $user_id);
                $userTransactions->__set("other_details", $transaction_type);
                $userTransactions->__set("gateway_transaction_id", $txnid);
                $userTransactions->__set("trnx_amount", $amount);
                $userTransactions->__set("trnx_method", $code);
                $userTransactions->__set("trnx_status", $status);
                $userTransactions->__set("trnx_order_id", $id);

                $userTransactionsMapper->addNewUserTransaction($userTransactions);
            }

            if ($hash != $posted_hash) {
                throw new Exception("Invalid Transaction");
            } else {
                $flag = TRUE;
                $this->view->flag = $flag;

                 if ($transaction_type == "Package") {

                    //echo "in";
                    $package = $packagesMapper->getPackageById($package_id);


                    $number_clothes = $package->__get("no_of_clothes");

                    $number_pickups = $package->__get("no_of_pickups");
                    //echo $number_pickups;exit;
                    $validity = $package->__get("validity");
                    //echo $validity;exit;

                    $zend_date = new Zend_Date();
                    $date = $zend_date->addMonth($validity);
                    $new_date = $date->toString("dd-MM-yyyy");

                    $userTrack->__set("usertrack_user_id",$user_id);
                    $userTrack->__set("track_type",'package');
                    $userTrack->__set("usertrack_package_id",$package_id);
                    $userTrack->__set("clothes_left",$number_clothes);
                    $userTrack->__set("clothes_availed",$number_clothes);
                    $userTrack->__set("pickups_left",$number_pickups);
                    $userTrack->__set("pickups_availed",$number_pickups);
                    $userTrack->__set("usertrack_start_date",date('Y-m-d'));
                    $userTrack->__set("usertrack_expiry_date",$new_date);
                    
                    if ($userTrackMapper->addNewTrack(addNewTrack)) {
                        $this->view->hasMessage = true;
                        $this->view->messageType = "success";
                        $this->view->message = "Profile Updated successfully";
                    } else {
                        $this->view->hasMessage = true;
                        $this->view->messageType = "danger";
                        $this->view->message = "Error while updating";
                    }
                } elseif ($transaction_type == "Online") {
                    $order = $ordersMapper->getOrderById($id);

                    $order->__set("order_payment_status", "Paid");

                    $ordersMapper->updateOrder($order);
                }
                $this->_redirect('index/orderlist');
            }
        } catch (Exception $e) {
            $e->getMessage();
        }
    }

    public function failureAction() {
        print_r($_POST);
    }

}
