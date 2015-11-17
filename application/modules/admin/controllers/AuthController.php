    <?php 

class Admin_AuthController extends Zend_Controller_Action {

    protected $_auth;

    public function init() {
        $this->_auth = new My_Auth("admin");
        $this->_helper->layout()->setlayout('admin_login');
    }

    public function indexAction() {
        // action body
        $request = $this->getRequest();
        $request_type = $request->getParam("request_type", false);
        $username = $request->getParam("admin_username");
        $password = $request->getParam("hashed_password");
        if ($request->isPost()) {

            if ($request_type) {
                if ($request_type == "login") {

                    $errors = array();
                    if (empty($username)) {
                        $errors[] = "Username Should not be empty";
                    }
                    if (empty($password)) {
                        $errors[] = "Password Should not be empty";
                    }
                    if (count($errors) == 0) {
                        if ($this->_process($request->getParams())) {

                            $admin_id = $this->_auth->getIdentity()->admin_id;
                            $this->_helper->redirector('index', 'index');
                        } else {
                            $this->view->hasMessage = true;
                            $this->view->messageType = "danger";
                            $this->view->message = "Invalid username and password";
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

    protected function _process($values) {

        // Get our authentication adapter and check credentials
        $adapter = $this->_getAuthAdapter();

        $adapter->setIdentity($values['admin_username']); // set identity for username

        $adapter->setCredential($values['hashed_password']);

        $adminAuth = new My_Auth("admin"); // session create

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

    protected function _getAuthAdapter() {

        $admins = new Application_Model_DbTable_Admins();

        $authAdapter = new Zend_Auth_Adapter_DbTable($admins->getAdapter());

        $authAdapter->setTableName('admins')
                ->setIdentityColumn('admin_username')
                ->setCredentialColumn('hashed_password')
                ->setCredentialTreatment('SHA1(?)');

        return $authAdapter;
    }

    public function logoutAction() {
        $adminAuth = new My_Auth("admin");
        $adminAuth->clearIdentity();
        $this->_helper->redirector('index', 'auth');
    }

    public function forgotPasswordAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $email = $request->getParam("admin_email");
            $adminsMapper = new Application_Model_AdminsMapper();
            if (!$email) {
                $this->view->hasMessage = true;
                $this->view->messageType = "danger";
                $this->view->message = "Enter Your email first";
            } else {
                $admin = $adminsMapper->getAdminByEmail($email);
                //print_r($admin); exit;
                if (!$admin) {
                    $this->view->hasMessage = true;
                    $this->view->messageType = "danger";
                    $this->view->message = "Email Address doesn't exists";
                } else {
                    $reset_code = mt_rand() . mt_rand() . mt_rand() . mt_rand() . mt_rand();
                    $admin->__set("reset_code", $reset_code);
                    if ($adminsMapper->updateAdmin($admin)) {
                        $subject = $admin->__get("admin_fname") . " " . $admin->__get("admin_lname") . ", Your password reset link from Laundrwala.co.in";

                        $message = "Dear " . $admin->__get("admin_fname") . " " . $admin->__get("admin_lname") . ",<br/><br/>
                            Please check your login details from laundrywala.co.in as below.<br/><br/>
                            Your registered email address : " . $admin->__get("admin_email") . "<br/><br/>
                            And Your Username : " . $admin->__get("admin_username") . "<br/><br/>
                            Password reset link <a href='" . $this->view->baseUrl() . "/admin/auth/reset-password/code/" . $reset_code . "'>" . $this->view->baseUrl() . "/admin/auth/reset-password/code/" . $reset_code . "</a><br/><br/>
                            Thanks and Regards,<br/>
                            For LMS.com<br/>
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

            $adminsMapper = new Application_Model_AdminsMapper();
            $admin = $adminsMapper->getAdminByResetCode($code);
            if (!$admin) {

                $this->view->hasMessage = true;
                $this->view->messageType = "danger";
                $this->view->message = "Invalid reset code";
            }

            if ($request->isPost()) {
                $password = $request->getParam("new_pass");
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

                        $admin->__set("hashed_password", $hashed_password);
                        $admin->__set("reset_code", "");
                        if ($adminsMapper->updateAdmin($admin)) {
                            $this->view->hasMessage = true;
                            $this->view->messageType = "success";
                            $this->view->message = "Password reset successful. <a href='" . $this->view->baseUrl() . "/admin/auth'>Click here</a> to login.";
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

}
