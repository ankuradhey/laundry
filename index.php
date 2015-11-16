<?php
//require_once __DIR__ . '/../init.php';
// define path to application directory

define('T_PREFIX', 'stip_');
defined('APPLICATION_PATH') || define('APPLICATION_PATH', __DIR__ . '/application');

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));
defined('WEBSITE_PATH')
    || define('WEBSITE_PATH', realpath(dirname(__FILE__) . '/'));
// set include path
set_include_path(realpath(APPLICATION_PATH . '/../library') . PATH_SEPARATOR . get_include_path());

require_once 'Zend/Config/Ini.php';
require_once 'Zend/Application.php';

class Application
{

	public static $env;

	public static function bootstrap($resource = null)
	{
            include_once 'Zend/Loader/Autoloader.php';
            $autoloader = Zend_Loader_Autoloader::getInstance();
            $autoloader->registerNamespace('Vslice_');
            $autoloader->registerNamespace('ZFDebug');
           // $autoloader->registerNamespace('Bvb');
            $autoloader->registerNamespace('My');
            $autoloader->registerNamespace('F5');
            $autoloader->registerNamespace('Zendx');
            $options = array(
            'jquery_path' => 'http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js',
            'plugins' => array('Variables',
            'Html',
            //    'Database' => array('adapter' => array('standard' => $db)),
            'File' => array('base_path' => '/Library/WebServer/Documents/'),
            'Memory',
            'Time',
            //  'Cache' => array('backend' => $cache->getBackend()),
            'Exception')
            );

            $debug = new ZFDebug_Controller_Plugin_Debug($options);
            $application = new Zend_Application(self::_getEnv(), self::_getConfig());
            return $application->getBootstrap()->bootstrap($resource);
	}

	public static function run()
	{
		self::bootstrap()->run();
	}

	private static function _getEnv()
	{
		return self::$env ? : APPLICATION_ENV;
	}

	private static function _getConfig()
	{
		$env = self::_getEnv();
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', $env, true);
		return $config->toArray();
	}
        
}

Application::run();