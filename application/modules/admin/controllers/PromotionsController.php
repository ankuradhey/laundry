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
}