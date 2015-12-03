<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use ZF\Apigility\Provider\ApigilityProviderInterface;
use Application\Model\Notification;
use Application\Model\NotificationTable;
class Module implements ApigilityProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getServiceConfig(){
        return array(
          'factories'=> array(
              'NotificationTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new User());
                    return new TableGateway('notification', $dbAdapter, array(new Feature\MasterSlaveFeature($sm->get('SlaveAdapter')), new Feature\MasterSlaveFeature($sm->get('SlaveAdapter2')), new Feature\MasterSlaveFeature($sm->get('SlaveAdapter3'))), $resultSetPrototype);
                },
              'NotificationTable' => function ($sm) {
                    $tableGateway = $sm->get('QuestionTableGateway');
                    $table = new QuestionTable($tableGateway);
                    return $table;
                },
              'Application\V1\Rest\Notification\NotificationMapper' =>  function ($sm) {
                $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                return new \Application\V1\Rest\Notification\NotificationMapper($adapter);
              },
              'Application\V1\Rest\User\UserMapper' =>  function ($sm) {
                $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                return new \Application\V1\Rest\User\UserMapper($adapter);
              },
              'Application\V1\Rest\Address\AddressMapper' =>  function ($sm) {
                $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                return new \Application\V1\Rest\Address\AddressMapper($adapter);
              },
              'Application\V1\Rest\Order\OrderMapper' =>  function ($sm) {
                $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                return new \Application\V1\Rest\Order\OrderMapper($adapter);
              },
              'Application\V1\Rest\Rate\RateMapper' =>  function ($sm) {
                $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                return new \Application\V1\Rest\Rate\RateMapper($adapter);
              },
              'Application\V1\Rest\Offer\OfferMapper' =>  function ($sm) {
                $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                return new \Application\V1\Rest\Offer\OfferMapper($adapter);
              },
              'Application\V1\Rest\Package\PackageMapper' =>  function ($sm) {
                $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                return new \Application\V1\Rest\Package\PackageMapper($adapter);
              },
              'Application\V1\Rest\Page\PageMapper' =>  function ($sm) {
                $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                return new \Application\V1\Rest\Page\PageMapper($adapter);
              },
              'Application\V1\Rest\Service\ServiceMapper' =>  function ($sm) {
                $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                return new \Application\V1\Rest\Service\ServiceMapper($adapter);
              },
              'Application\V1\Rest\Category\CategoryMapper' =>  function ($sm) {
                $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                return new \Application\V1\Rest\Category\CategoryMapper($adapter);
              },
              'Application\V1\Rest\Location\LocationMapper' =>  function ($sm) {
                $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                return new \Application\V1\Rest\Location\LocationMapper($adapter);
              },
          )  
        );
    }
}
