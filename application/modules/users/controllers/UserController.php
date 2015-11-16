<?php

class Users_UserController extends Vslice_AbstractController
{

    public function init()
    {
           $this->db = Zend_Registry::get('db');
          // Bvb_Grid_Deploy_JqGrid::$debug = true;
           ZendX_JQuery::enableView($this->view);

        
   }
	public function indexAction()
	{
		$this->_helper->getHelper('layout')->setLayoutPath(APPLICATION_PATH . '/layouts/scripts');
		$this->_helper->getHelper('layout')->setLayout('layout');
	}

}