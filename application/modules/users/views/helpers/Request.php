<?php
 
/**
 * @category   Zend
 * @package    Zend_View
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
require_once 'Zend/View/Helper/FormElement.php';
 
class Zend_View_Helper_Request extends Zend_View_Helper_FormElement
{
    protected $request;
 
    public function __construct()
    {
        if (null === $this->request) {  
            $this->request = Zend_Controller_Front::getInstance()->getRequest();
        }
    }
     
    public function request($label, $value = null, $superglobal = 'all')
    {   
        switch($superglobal) {          
            case 'get'   : return $this->request->getQuery($label, $value);
            case 'post'  : return $this->request->getPost($label, $value);
            case 'cookie': return $this->request->getCookie($label, $value);
            case 'server': return $this->request->getServer($label, $value);
            case 'env'   : return $this->request->getEnv($label, $value);
            case 'all'   : 
            default      :  return $this->request->get($label, $value);
        }
    }
}