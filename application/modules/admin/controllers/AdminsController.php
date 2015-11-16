<?php 

class Admin_AdminsController extends Zend_Controller_Action {

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

            $admin_form = new Application_Form_AdminForm();
            $this->view->form = $admin_form;

            $adminsMapper = new Application_Model_AdminsMapper();
            $admins = new Application_Model_Admins();

            $request = $this->getRequest();
            if ($request->isPost()) {

                $request_type = $request->getParam("request_type", false);
                if ($request_type) {

                    if ($request_type == "add") {
                        $params = $request->getParams();

                        if ($admin_form->isValid($params)) {
                            foreach ($params as $param => $value) {

                                if($param=="hashed_password")
                                {
                                    $value = sha1($value);
                                }
                                $admins->__set($param, $value);
                            }
                            if ($adminsMapper->addNewAdmin($admins)) {
                                $this->view->message = "Admin added successfully";
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
                        if ($adminsMapper->deleteAdminById($id)) {
                            $this->view->hasMessage = true;
                            $this->view->messageType = "success";
                            $this->view->message = "Admin deleted successfully.";
                        } else {
                            $this->view->hasMessage = true;
                            $this->view->messageType = "danger";
                            $this->view->message = "Error occured while deleting. Please try again.";
                        }
                    }
                }
            }
            $admin = $adminsMapper->getAllAdmins();
            $this->view->admin = $admin;
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
            //$admins = new Application_Model_Admins;
            $adminsMapper = new Application_Model_AdminsMapper;

            $request = $this->getRequest();

            $admin_form = new Application_Form_AdminForm();
            $admin_form->removeElement("hashed_password");
            $admin_form->removeElement("cpassword");
            $admin_form->removeElement("admin_email");

            $elements = $admin_form->getElements();
            $this->view->form = $admin_form;

            $admin_form_password = new Application_Form_AdminForm();
            $this->view->password_form = $admin_form_password;

            $admin_id = $request->getParam("id");

            $secure = new My_Secure();
            $id = $secure->decode($admin_id);

            $admin = $adminsMapper->getAdminById($id);

            $this->view->admin = $admin;

            foreach ($elements as $element) {
                $element->setValue($admin->__get($element->getName()));
            }

            if ($request->isPost()) {

                $request_type = $request->getParam("request_type", false);
                if ($request_type) {
                    if ($request_type == "edit") {
                        $params = $request->getParams();

                        if ($admin_form->isValid($params)) {
                            foreach ($params as $param => $value) {

                                $admin->__set($param, $value);
                            }
                            if ($adminsMapper->updateAdmin($admin)) {
                                $this->view->message = "Admin Updated successfully";
                                $this->view->hasMessage = true;
                                $this->view->messageType = "success";
                            } else {
                                $this->view->message = "Error occured while updating. Please try again";
                                $this->view->hasMessage = true;
                                $this->view->messageType = "danger";
                            }
                        } else {
                            $this->view->message = "Error occured while updating. Please fill form correctly";
                            $this->view->hasMessage = true;
                            $this->view->messageType = "danger";
                        }
                    } elseif ($request_type == "change_password") {

                        $pass = $request->getParam("hashed_password");
                        $cpaas = $request->getParam("cpassword");

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
                            $hashed_password = $admin->__get("hashed_password");

                            $hashed_password = sha1($pass);
                            if (count($errors) == 0) {
                                $admin->__set("hashed_password", $hashed_password);
                                if ($adminsMapper->updateAdmin($admin)) {
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
            $this->authorised = true;
        } catch (Exception $ex) {
            $this->authorised = false;
            $this->view->hasMessage = true;
            $this->view->messageType = "danger";
            $this->view->message = $ex->getMessage();
        }
    }

}
