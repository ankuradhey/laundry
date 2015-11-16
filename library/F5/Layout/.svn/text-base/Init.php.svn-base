<?php

class F5_Layout_Init extends Zend_Controller_Plugin_Abstract {
 
    protected $_moduleLayouts;

    public function registerModuleLayout($module, $layoutPath, $layout=null){
        $this->_moduleLayouts[$module] = array(
            'layoutPath' => $layoutPath,
            'layout' => $layout
        );
    }
 
    public function routeShutdown(Zend_Controller_Request_Abstract $request){
        if(isset($this->_moduleLayouts[$request->getModuleName()])){
            $config = $this->_moduleLayouts[$request->getModuleName()];
 
            $layout = Zend_Layout::getMvcInstance();
            if($layout->getMvcEnabled()){

                if($request->getControllerName() == 'admin') {
                    
                	$layout->setLayoutPath(APPLICATION_PATH . '/modules/admin/layouts/scripts');
                    
                } else {
                    $layout->setLayoutPath($config['layoutPath']);
     
                    if($config['layout'] !== null){
                        $layout->setLayout($config['layout']);
                        
                    }
                }
            }
        }
    }
}

?>