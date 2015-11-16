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
}