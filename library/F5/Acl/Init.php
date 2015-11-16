<?php

class F5_Acl_Init extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $resource = new User_Model_Acl_Resource();
        $resource->getPrivileges($request);

        if(!$resource->privileges || !$resource->resource_id) { //error in getting resource privileges or nobody is allowed access, deny access and redirect to forbidden
            return $request->setModuleName('default')->setControllerName('error')->setActionName('forbidden');
        }

        $acl = new Zend_Acl();
        $acl->add(new Zend_Acl_Resource($resource->resource_id));
     
        foreach($resource->privileges as $key => $privilege) {
            if(!$acl->hasRole($privilege["role_id"])) {
                $acl->addRole(new Zend_Acl_Role($privilege["role_id"]));
                $acl->allow($privilege["role_id"], $resource->resource_id);
            }
        }
             
        $authorization = Zend_Auth::getInstance();
            
        if($authorization->hasIdentity()) { //SET THE CORECT REDIRECTIONS 

            if($acl->isAllowed(null, $resource->resource_id)) { //any role has access
                return; 
            }
                    
            $user = $authorization->getIdentity();

            if($acl->hasRole($user['role_id']) && $acl->isAllowed($user['role_id'], $resource->resource_id)) { //role has access
                return; 
            }
            
            //user role does not have access to this resource
            return $request->setModuleName('default')->setControllerName('error')->setActionName('noauth');
 
        } else {
            
            $aclrole = new User_Model_Acl_Role();
            $aclrole->getDefaultRole();

            if(!$aclrole->default_role || !$acl->hasRole($aclrole->default_role) || !$acl->isAllowed($aclrole->default_role, $resource->resource_id)) { //redirect to login
                 return $request->setModuleName('admin')->setControllerName('admin')->setActionName('login');
            }
        }
    }

    public function checkAccess(Zend_Controller_Request_Abstract $request)
    {
        $resource = new User_Model_Acl_Resource();
        $resource->getPrivileges($request);

        if(!$resource->privileges || !$resource->resource_id) { //error in getting resource privileges or nobody is allowed access, deny access and redirect to forbidden
            return false;
        }

        $acl = new Zend_Acl();
        $acl->add(new Zend_Acl_Resource($resource->resource_id));
     
        foreach($resource->privileges as $key => $privilege) {
            if(!$acl->hasRole($privilege["role_id"])) {
                $acl->addRole(new Zend_Acl_Role($privilege["role_id"]));
                $acl->allow($privilege["role_id"], $resource->resource_id);
            }
        }
        
        $authorization = Zend_Auth::getInstance();

        if($authorization->hasIdentity()) {

            $user = $authorization->getIdentity();

            if($acl->hasRole($user['role_id']) && $acl->isAllowed($user['role_id'], $resource->resource_id)) { //role has access
                return true; 
            }
            
            //user role does not have access to this resource
            return false;
 
        } else {
            
            $aclrole = new User_Model_Acl_Role();
            $aclrole->getDefaultRole();

            if(!$aclrole->default_role || !$acl->hasRole($aclrole->default_role) || !$acl->isAllowed($aclrole->default_role, $resource->resource_id)) { //redirect to login
                return false;
            }
        }
        
        return true;
    }
/*
    public function checkAssociationAccess(Zend_Controller_Request_Abstract $request)
    {
        $resource = new User_Model_Acl_Resource();
        $resource->getPrivileges($request);

        if(!$resource->privileges || !$resource->resource_id) { //error in getting resource privileges or nobody is allowed access, deny access and redirect to forbidden
            return false;
        }

        $acl = new Zend_Acl();
        $acl->add(new Zend_Acl_Resource($resource->resource_id));
     
        foreach($resource->privileges as $key => $privilege) {
            if(!$acl->hasRole($privilege["role_id"])) {
                $acl->addRole(new Zend_Acl_Role($privilege["role_id"]));
                $acl->allow($privilege["role_id"], $resource->resource_id);
            }
        }
        
        $authorization = Zend_Auth::getInstance();

        if($authorization->hasIdentity()) {

            $user = $authorization->getIdentity();

            if($acl->hasRole($user['role_id']) && $acl->isAllowed($user['role_id'], $resource->resource_id)) { //role has access
                return true; 
            }
            
            //user role does not have access to this resource
            return false;
 
        } else {
            
            $aclrole = new User_Model_Acl_Role();
            $aclrole->getDefaultRole();

            if(!$aclrole->default_role || !$acl->hasRole($aclrole->default_role) || !$acl->isAllowed($aclrole->default_role, $resource->resource_id)) { //redirect to login
                return false;
            }
        }
        
        return true;
    }
*/
}

?>