<?php

class Admin_CouponsController extends Zend_Controller_Action {

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

            $coupon_form = new Application_Form_CouponForm();
            $this->view->form = $coupon_form;

            $couponsMapper = new Application_Model_CouponsMapper();
            $coupons = new Application_Model_Coupons();

			

            $request = $this->getRequest();
            if ($request->isPost()) {

                $request_type = $request->getParam("request_type", false);
				
                if ($request_type) {

                    if ($request_type == "add") {
                        $params = $request->getParams();

                        if ($coupon_form->isValid($params)) {
							
                            foreach ($params as $param => $value) {

                                $coupons->__set($param, $value);
                            }
							
                            if ($couponsMapper->addNewCoupon($coupons)) {
								
                                $this->view->message = "Coupon added successfully";
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
                        if ($couponsMapper->deleteCouponById($id)) {
                            $this->view->hasMessage = true;
                            $this->view->messageType = "success";
                            $this->view->message = "Coupon deleted successfully.";
                        } else {
                            $this->view->hasMessage = true;
                            $this->view->messageType = "danger";
                            $this->view->message = "Error occured while deleting. Please try again.";
                        }
                    }
                }
            }
            $coupon = $couponsMapper->getAllCoupons();
            $this->view->coupons = $coupon;
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
            $couponsMapper = new Application_Model_CouponsMapper;

            $request = $this->getRequest();

            $coupon_form = new Application_Form_CouponForm();

            $elements = $coupon_form->getElements();
            $this->view->form = $coupon_form;

            $coupon_id = $request->getParam("id");

            $secure = new My_Secure();
            $id = $secure->decode($coupon_id);

            $coupon = $couponsMapper->getCouponById($id);

            $this->view->coupon = $coupon;

            foreach ($elements as $element) {
                $element->setValue($coupon->__get($element->getName()));
            }

            if ($request->isPost()) {

                $request_type = $request->getParam("request_type", false);
                if ($request_type) {
                    if ($request_type == "edit") {
                        $params = $request->getParams();

                        if ($coupon_form->isValid($params)) {
                            foreach ($params as $param => $value) {

                                $coupon->__set($param, $value);
                            }
							
                            if ($couponsMapper->updateCoupon($coupon)) {
                                $this->view->message = "Coupon Updated successfully";
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
