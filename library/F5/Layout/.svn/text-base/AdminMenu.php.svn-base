<?php

class F5_Layout_AdminMenu extends Zend_Controller_Plugin_Abstract
{
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        if(!in_array(Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName(), array('admin', 'admin_language'))) {
        	return;
        }
        
        $resource = new User_Model_Acl_Resource();
        $resource->getAdminPrivileges();
              
        if($resource->admin_privileges) {
            
            //$actionStack = Zend_Controller_Action_HelperBroker::getStaticHelper('ActionStack');
            $actionStack = new Zend_Controller_Plugin_ActionStack();

        	foreach ($resource->admin_privileges as $module => $actions) {

                $class = ucfirst($module) . '_AdminController';
                           
                if(!class_exists($class)) {
                    Zend_Loader::loadFile(APPLICATION_PATH . '/modules/' . $module . '/controllers/AdminController.php');
                }

                $reflection = new Zend_Reflection_Class($class);
                
                $method = null; 
                
                try {
					if($method = $reflection->getMethod('menuAction')) { 
						$actionStack->pushStack(new Zend_Controller_Request_Simple('menu', 'admin', $module, array('admin_actions' => array_flip($actions))));
					}       
				} catch (Exception $e) {
				}                
			}
        }
    }
}

?>