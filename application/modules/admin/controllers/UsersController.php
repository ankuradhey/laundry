<?php

class Admin_UsersController extends Zend_Controller_Action {

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

            $user_form = new Application_Form_UserForm();
            $this->view->form = $user_form;

            $usersMapper = new Application_Model_UsersMapper();
            $users = new Application_Model_Users();

            $request = $this->getRequest();
            if ($request->isPost()) {

                $request_type = $request->getParam("request_type", false);
                if ($request_type) {

                    if ($request_type == "add") {
                        $params = $request->getParams();

                        if ($user_form->isValid($params)) {
                            foreach ($params as $param => $value) {
                                if ($param == "hashed_password") {
                                    $value = sha1($value);
                                }
                                $users->__set($param, $value);
                            }
                            $users->__set("user_fb_id", "");

                            if ($usersMapper->addNewUser($users)) {
                                $this->view->message = "User added successfully";
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
                        if ($usersMapper->deleteUserById($id)) {
                            $this->view->hasMessage = true;
                            $this->view->messageType = "success";
                            $this->view->message = "User deleted successfully.";
                        } else {
                            $this->view->hasMessage = true;
                            $this->view->messageType = "danger";
                            $this->view->message = "Error occured while deleting. Please try again.";
                        }
                    }
                }
            }
            $user = $usersMapper->getAllUsers();
            $this->view->users = $user;
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
            $usersMapper = new Application_Model_UsersMapper;

            $request = $this->getRequest();

            $user_form = new Application_Form_UserForm();
            $user_form->removeElement("hashed_password");
            $user_form->removeElement("cpassword");
            $user_form->removeElement("user_email");

            $elements = $user_form->getElements();
            $this->view->form = $user_form;

            $user_form_password = new Application_Form_UserForm();
            $this->view->password_form = $user_form_password;

            $user_id = $request->getParam("id");

            $secure = new My_Secure();
            $id = $secure->decode($user_id);

            $users = $usersMapper->getUserById($id);

            foreach ($elements as $element) {
                $element->setValue($users->__get($element->getName()));
            }

            if ($request->isPost()) {

                $request_type = $request->getParam("request_type", false);
                if ($request_type) {
                    if ($request_type == "edit") {
                        $params = $request->getParams();

                        if ($user_form->isValid($params)) {
                            foreach ($params as $param => $value) {

                                $users->__set($param, $value);
                            }
                            $users->__set("user_fb_id", "");

                            if ($usersMapper->updateUser($users)) {
                                $this->view->message = "User Updated successfully";
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
                            $hashed_password = $users->__get("hashed_password");

                            $hashed_password = sha1($pass);
                            if (count($errors) == 0) {
                                $users->__set("hashed_password", $hashed_password);

                                if ($usersMapper->updateUser($users)) {
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
