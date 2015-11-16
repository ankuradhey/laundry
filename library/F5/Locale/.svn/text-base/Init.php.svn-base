<?php

class F5_Locale_Init extends Zend_Controller_Plugin_Abstract
{
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $translate = Zend_Registry::get('Zend_Translate');
        $locale = Zend_Registry::get('Zend_Locale');
        $languages = Zend_Registry::get('languages');
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();        
        $view = Zend_Layout::getMvcInstance()->getView();
        //$view->route = '';
  
        if($language = strtolower($request->getParam('language'))) {

            if(($language != $locale->getLanguage($translate->getLocale())) && Zend_Locale::isLocale($language)) {
                
                try {
					$translate->addTranslation(APPLICATION_PATH . '/../data/locales/' . $language . '.php', $language);

                    if($translate->isAvailable($language)) {
                        $locale->setLocale($languages[$language]);
                        $router->setGlobalParam('language', $language);    
                                        	
                        Zend_Registry::set('Zend_Translate', $translate);
                        Zend_Registry::set('Zend_Locale', $locale);
                        
                        //$view->route .= '_language';
                    }
                                
				} catch (Exception $e) {

				}
            }
        }
        
        $view->route = $router->getCurrentRouteName();
/*        
echo $router->getCurrentRouteName();
echo '
  <h3>Request Parameters:</h3>
  <pre> ' . var_export($request->getParams(), true) . '
  </pre>
'; 
//die();
*/        
    }
}

?>