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
	
	public function addAction() {
		
		$this->view->pageHeading = "Add package";
		$this->view->buttonTitle = "Add package";
		
        try {
            //$admins = new Application_Model_Admins;
            $packagesMapper = new Application_Model_PackagesMapper;
			$package = new Application_Model_Packages();			
			
            $request = $this->getRequest();

            $package_form = new Application_Form_PackageForm();
			
            $this->view->form = $package_form;

            $elements = $package_form->getElements();                                    
                        			
            if($request->isPost()) {
				
                $request_type = $request->getParam("request_type", false);
				
				$params = $this->getRequest()->getPost(); 			  			
								
                if($package_form->isValid($params)) {
							
							$packageIconName = $this->_imageUpload('package_icon', 'file');
							
							$packageIconHoverName = $this->_imageUpload('package_icon_hover', 'file');
															
                            foreach ($params as $param => $value) {

                                $package->__set($param, $value);
								
                            }
							$package->__set("package_icon", $packageIconName);
							$package->__set("package_icon_hover", $packageIconHoverName);
														
							$isInserted = $packagesMapper->addNewPackage($package);
							
                            if (is_object($isInserted) && $isInserted->success) {

								$this->view->message = "Package added successfully";
                                $this->view->hasMessage = true;
                                $this->view->messageType = "success";								

                            } else {

                                $this->view->message = "Error occured while updating. Please try again";
                                $this->view->hasMessage = true;
                                $this->view->messageType = "danger";

                            }
                        } 
				else {										
					
					$this->view->message = "Error occured while adding package. Please fill form correctly";
					$this->view->hasMessage = true;
					$this->view->messageType = "danger";
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
	
	public function editAction() {
		
		$this->view->pageHeading = "Edit package";
		$this->view->buttonTitle = "Edit package";
		
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
				                
                        $params = $request->getParams();

                        if ($package_form->isValid($params)) {
							
							
							$packageIconName = $this->_imageUpload('package_icon', 'file');
							$packageIconHoverName = $this->_imageUpload('package_icon_hover', 'file');
																						
                            foreach ($params as $param => $value) {

                                $package->__set($param, $value);
								
                            }
							
							if(!empty($packageIconName))
							$package->__set("package_icon", $packageIconName);
							if(!empty($packageIconHoverName))
							$package->__set("package_icon_hover", $packageIconHoverName);
														
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
			
            $this->authorised = true;
			
        } catch (Exception $ex) {
            $this->authorised = false;
            $this->view->hasMessage = true;
            $this->view->messageType = "danger";
            $this->view->message = $ex->getMessage();
        }
		
		$this->render("add");
		
    }
	
	protected function _imageUpload($input_name, $file_prefix = "file") {
        $adapter = new Zend_File_Transfer_Adapter_Http();
        //$adapter->addValidator('Extension', false, 'jpg,png,gif');

        defined('PUBLIC_PATH') || define('PUBLIC_PATH', realpath(dirname(dirname(dirname(dirname(dirname(__FILE__)))))));
        $files = $adapter->getFileInfo();
        
        $uniqId = strtotime(date("Y-m-d H:i:s")).'-'.uniqid();

        foreach ($files as $file => $info) {

            $newFilename= false;
            if ($file == $input_name and strlen($info["name"]) > 0) {
                $adapter->setDestination(PUBLIC_PATH . "/public/img/packages/");
                $originalFilename = pathinfo($adapter->getFileName($file));
                //$extesion = $file["extension"];
                $newFilename = $file_prefix . '-' . $uniqId . '.' . $originalFilename['extension'];
                $adapter->addFilter('Rename', $newFilename, $file);
                $adapter->receive($file);                
				
				return $newFilename;
            }

            
        }
    }
	
}