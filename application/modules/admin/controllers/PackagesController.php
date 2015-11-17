<?php

class Admin_PackagesController extends Zend_Controller_Action{
    
    public function init() {

        $this->_auth = new My_Auth("admin");
        if (!$this->_auth->hasIdentity()) {
            $this->_helper->redirector('index', 'auth');
        }
        $this->_helper->layout()->setLayout('admin');
    }
    
    function indexAction(){
        try{
            $packagesMapper = new Application_Model_PackagesMapper();
            $packages = $packagesMapper->getAllPackages();
            $this->view->packages = $packages;
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
            $packagesMapper = new Application_Model_PackagesMapper;

            $request = $this->getRequest();

            $package_form = new Application_Form_PackageForm();
            $this->view->form = $package_form;

            $elements = $package_form->getElements();

            $id = $request->getParam("id");
            
            $package = $packagesMapper->getPackageById($id);

            foreach ($elements as $element) {
                $element->setValue($package->__get($element->getName()));
            }

            if($request->isPost()) {
				
                $request_type = $request->getParam("request_type", false);
                if ($request_type) {
                    if ($request_type == "edit"){
                        $params = $request->getParams();

                        if ($package_form->isValid($params)) {
                            foreach ($params as $param => $value) {

                                $package->__set($param, $value);
								
                            }
							
							$isUpdated = $packagesMapper->updatePackage($package);
							
                            if (is_object($isUpdated) && $isUpdated->success) {
                                
								if($isUpdated->row_affected>0)
									$this->view->message = "Package Updated successfully";
								else
									$this->view->message = "No Data Updated";
								
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