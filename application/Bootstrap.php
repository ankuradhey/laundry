<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected $_docRoot;

    protected function _initPath() {
        $this->_docRoot = realpath(APPLICATION_PATH . '/../');
        Zend_Registry::set('docRoot', $this->_docRoot);
    }

//End Function

    protected function _initLoaderResource() {
        error_reporting(-1);
        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
            'basePath' => $this->_docRoot . '/application',
            'namespace' => 'Vslice'
        ));

        $resourceLoader->addResourceTypes(array(
            'model' => array(
                'namespace' => 'Model',
                'path' => 'models'
            )
        ));
    }

//End Function

    protected function _initRoutes() {
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();

        $route = new Zend_Controller_Router_Route('sociallogin/:provider', array(
            'controller' => 'index',
            'action' => 'index'
        ));
        $router->addRoute('sociallogin/:provider', $route);

        $route = new Zend_Controller_Router_Route_Static('login', array(
            'controller' => 'index',
            'action' => 'login'
        ));
        $router->addRoute('login', $route);

        $route = new Zend_Controller_Router_Route_Static('logout', array(
            'controller' => 'index',
            'action' => 'logout'
        ));
        $router->addRoute('index', $route);
    }

    protected function _initDoctype() {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        Zend_Registry::set('config', $config);
        $db = Zend_Db::factory($config->resources->db);
        Zend_Registry::set('db', $db);




        $locale = new Zend_Locale($config->resources->locale->default);
        $translate = new Zend_Translate('array', APPLICATION_PATH . '/../data/locales/' . $locale->getLanguage($config->resources->locale->default) . '.php', $config->resources->locale->default, array('disableNotices' => TRUE));

        $languages = array(
            'en' => 'en_US'
        );

        Zend_Validate_Abstract::setDefaultTranslator($translate);

        Zend_Registry::set('db', $db);
        Zend_Registry::set('languages', $languages);
        Zend_Registry::set('Zend_Translate', $translate);
        Zend_Registry::set('Zend_Locale', $locale);
    }

//End Function

    protected function _initConstants() {
        date_default_timezone_set("Asia/Kolkata"); 
        $registry = Zend_Registry::getInstance();
        $registry->constants = new Zend_Config($this->getApplication()->getOption('constants'));
    }
	
	
	
}

function pr($array){
	echo "<pre>";
	print_r($array);
	echo "</pre>";
}
	
function prd($array){
	pr($array);die;
}

//End Class