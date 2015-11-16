<?php 

class Admin_ServiceMasterController extends Zend_Controller_Action {

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

            $service_form = new Application_Form_ServiceMasterForm();
            $this->view->form = $service_form;

            $serviceMasterMapper = new Application_Model_ServiceMasterMapper();
            $services = new Application_Model_ServiceMasters();
            
            
            $request = $this->getRequest();
            if ($request->isPost()) {

                $request_type = $request->getParam("request_type", false);
                if ($request_type) {

                    if ($request_type == "add") {
                        
                        $params = $request->getParams();
                        
                        if ($service_form->isValid($params)) {
                            
                            foreach ($params as $param => $value) {

                                $services->__set($param, $value);
                            }
                            
                            if ($serviceMasterMapper->addNewServiceMaster($services)) {
                                $this->view->message = "Service added successfully";
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
                        if ($serviceMasterMapper->deleteServiceMasterById($id)) {
                            $this->view->hasMessage = true;
                            $this->view->messageType = "success";
                            $this->view->message = "Service deleted successfully.";
                        } else {
                            $this->view->hasMessage = true;
                            $this->view->messageType = "danger";
                            $this->view->message = "Error occured while deleting. Please try again.";
                        }
                    }
                }
            }
            $services = $serviceMasterMapper->getAllServiceMasters();
            $this->view->services = $services;
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
            $serviceMasterMapper = new Application_Model_ServiceMasterMapper();

            $request = $this->getRequest();

            $service_form = new Application_Form_ServiceMasterForm();
            $this->view->form = $service_form;

            $elements = $service_form->getElements();

            $service_id = $request->getParam("id");

            $secure = new My_Secure();
            $id = $secure->decode($service_id);

            $services = $serviceMasterMapper->getServiceMasterById($id);
            
            foreach ($elements as $element) {
                $element->setValue($services->__get($element->getName()));
            }

            if ($request->isPost()) {

                $request_type = $request->getParam("request_type", false);
                if ($request_type) {
                    if ($request_type == "edit") {
                        $params = $request->getParams();

                      
                        if ($service_form->isValid($params)) {
                            foreach ($params as $param => $value) {

                                $services->__set($param, $value);
                            }
                            
                            if ($serviceMasterMapper->updateServiceMaster($services)) {
                                $this->view->message = "Service Updated successfully";
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
