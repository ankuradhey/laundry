<?php

class Admin_PromotionsController extends Zend_Controller_Action{
    
    public function init() {

        $this->_auth = new My_Auth("admin");
        if (!$this->_auth->hasIdentity()) {
            $this->_helper->redirector('index', 'auth');
        }
        $this->_helper->layout()->setLayout('admin');
    }
    
    function indexAction(){
        try{
            $promotionsMapper = new Application_Model_PromotionsMapper();
            $promotions = $promotionsMapper->getAllOffers();
            $this->view->promotions = $promotions;
        } catch (Exception $ex) {
            $this->authorised = false;
            $this->view->hasMessage = true;
            $this->view->messageType = "danger";
            $this->view->message = $ex->getMessage();
        }
    }
	
	public function addAction() {
		
		$this->view->pageHeading = "Add promotion";
		$this->view->buttonTitle = "Add promotion";
		
        try {
            //$admins = new Application_Model_Admins;
            $promotionsMapper = new Application_Model_PromotionsMapper;
			$promotion = new Application_Model_Promotions();			
			
            $request = $this->getRequest();

            $form = new Application_Form_PromotionForm();
			
            $this->view->form = $form;

            $elements = $form->getElements();                                    
                        			
            if($request->isPost()) {
				
                $request_type = $request->getParam("request_type", false);
				
				$params = $this->getRequest()->getPost(); 			  			
								
                if($form->isValid($params)) {
							
							
                            foreach ($params as $param => $value) {

                                $promotion->__set($param, $value);
								
                            }
																					
							$isInserted = $promotionsMapper->addNewPromotion($promotion);
							
                            if (is_object($isInserted) && $isInserted->success) {

								$this->view->message = "Promotion added successfully";
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
		
		$this->view->pageHeading = "Edit promotion";
		$this->view->buttonTitle = "Update promotion";
		
        try {
            //$admins = new Application_Model_Admins;
            $promotionsMapper = new Application_Model_PromotionsMapper;

            $request = $this->getRequest();

            $form = new Application_Form_PromotionForm();
            $this->view->form = $form;

            $elements = $form->getElements();

            $id = $request->getParam("id");
            
            $promotion = $promotionsMapper->getPromotionById($id);
			
			$form->populate((array)$promotion);
			
            foreach ($elements as $element) {
                $element->setValue($promotion->__get($element->getName()));
            }

            if($request->isPost()) {
				                
                        $params = $request->getParams();

                        if ($form->isValid($params)) {
																					
                            foreach ($params as $param => $value) {

                                $promotion->__set($param, $value);
								
                            }														
							
							$isUpdated = $promotionsMapper->updatePromotion($promotion);
							
                            if (is_object($isUpdated) && $isUpdated->success) {
                                
								if($isUpdated->row_affected>0)
									$this->view->message = "Promotion Updated successfully";
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