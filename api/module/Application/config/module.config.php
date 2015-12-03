<?php
return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'Application\\Controller\\Index',
                        'action' => 'index',
                    ),
                ),
            ),
            'application' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(),
                        ),
                    ),
                ),
            ),
            'application.rest.notification' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/notifications[/:user_id]',
                    'defaults' => array(
                        'controller' => 'Application\\V1\\Rest\\Notification\\Controller',
                    ),
                ),
            ),
            'application.rest.user' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/users[/:user_id]',
                    'defaults' => array(
                        'controller' => 'Application\\V1\\Rest\\User\\Controller',
                    ),
                ),
            ),
            'application.rest.address' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/addresses[/:user_id][/:address_id]',
                    'defaults' => array(
                        'controller' => 'Application\\V1\\Rest\\Address\\Controller',
                    ),
                ),
            ),
            'application.rest.order' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/orders[/:user_id]',
                    'defaults' => array(
                        'controller' => 'Application\\V1\\Rest\\Order\\Controller',
                    ),
                ),
            ),
            'application.rest.rate' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/rates/city[/:city_id]/service[/:service_id]/category[/:category_id]/delivery[/:delivery_type]',
                    'defaults' => array(
                        'controller' => 'Application\\V1\\Rest\\Rate\\Controller',
                    ),
                ),
            ),
            'application.rest.offer' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/offers[/:offer_id]',
                    'defaults' => array(
                        'controller' => 'Application\\V1\\Rest\\Offer\\Controller',
                    ),
                ),
            ),
            'application.rest.package' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/packages[/:package_id]',
                    'defaults' => array(
                        'controller' => 'Application\\V1\\Rest\\Package\\Controller',
                    ),
                ),
            ),
            'application.rest.page' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/pages[/:page_key]',
                    'defaults' => array(
                        'controller' => 'Application\\V1\\Rest\\Page\\Controller',
                    ),
                ),
            ),
            'application.rest.service' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/services[/:city_id]',
                    'defaults' => array(
                        'controller' => 'Application\\V1\\Rest\\Service\\Controller',
                    ),
                ),
            ),
            'application.rest.category' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/categories[/:category_id]',
                    'defaults' => array(
                        'controller' => 'Application\\V1\\Rest\\Category\\Controller',
                    ),
                ),
            ),
            'application.rest.orderproduct' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/orderproduct[/:orderproduct_id]',
                    'defaults' => array(
                        'controller' => 'Application\\V1\\Rest\\Orderproduct\\Controller',
                    ),
                ),
            ),
            'application.rest.location' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/locations[/:location_id]',
                    'defaults' => array(
                        'controller' => 'Application\\V1\\Rest\\Location\\Controller',
                    ),
                ),
            ),
            'application.rest.city' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/city[/:city_id]',
                    'defaults' => array(
                        'controller' => 'Application\\V1\\Rest\\City\\Controller',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            0 => 'Zend\\Cache\\Service\\StorageCacheAbstractServiceFactory',
            1 => 'Zend\\Log\\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'translator' => 'Zend\\Mvc\\Service\\TranslatorServiceFactory',
            'Application\\V1\\Rest\\Notification\\NotificationResource' => 'Application\\V1\\Rest\\Notification\\NotificationResourceFactory',
            'Application\\V1\\Rest\\User\\UserResource' => 'Application\\V1\\Rest\\User\\UserResourceFactory',
            'Application\\V1\\Rest\\Address\\AddressResource' => 'Application\\V1\\Rest\\Address\\AddressResourceFactory',
            'Application\\V1\\Rest\\Order\\OrderResource' => 'Application\\V1\\Rest\\Order\\OrderResourceFactory',
            'Application\\V1\\Rest\\Rate\\RateResource' => 'Application\\V1\\Rest\\Rate\\RateResourceFactory',
            'Application\\V1\\Rest\\Offer\\OfferResource' => 'Application\\V1\\Rest\\Offer\\OfferResourceFactory',
            'Application\\V1\\Rest\\Package\\PackageResource' => 'Application\\V1\\Rest\\Package\\PackageResourceFactory',
            'Application\\V1\\Rest\\Page\\PageResource' => 'Application\\V1\\Rest\\Page\\PageResourceFactory',
            'Application\\V1\\Rest\\Service\\ServiceResource' => 'Application\\V1\\Rest\\Service\\ServiceResourceFactory',
            'Application\\V1\\Rest\\Category\\CategoryResource' => 'Application\\V1\\Rest\\Category\\CategoryResourceFactory',
            'Application\\V1\\Rest\\Orderproduct\\OrderproductResource' => 'Application\\V1\\Rest\\Orderproduct\\OrderproductResourceFactory',
            'Application\\V1\\Rest\\Location\\LocationResource' => 'Application\\V1\\Rest\\Location\\LocationResourceFactory',
            'Application\\V1\\Rest\\City\\CityResource' => 'Application\\V1\\Rest\\City\\CityResourceFactory',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            0 => array(
                'type' => 'gettext',
                'base_dir' => 'C:\\xampp\\htdocs\\work\\laundrywala\\api\\module\\Application\\config/../language',
                'pattern' => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\\Controller\\Index' => 'Application\\Controller\\IndexController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => 'C:\\xampp\\htdocs\\work\\laundrywala\\api\\module\\Application\\config/../view/layout/layout.phtml',
            'application/index/index' => 'C:\\xampp\\htdocs\\work\\laundrywala\\api\\module\\Application\\config/../view/application/index/index.phtml',
            'error/404' => 'C:\\xampp\\htdocs\\work\\laundrywala\\api\\module\\Application\\config/../view/error/404.phtml',
            'error/index' => 'C:\\xampp\\htdocs\\work\\laundrywala\\api\\module\\Application\\config/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            0 => 'C:\\xampp\\htdocs\\work\\laundrywala\\api\\module\\Application\\config/../view',
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(),
        ),
    ),
    'zf-versioning' => array(
        'uri' => array(
            0 => 'application.rest.notification',
            1 => 'application.rest.user',
            2 => 'application.rest.address',
            3 => 'application.rest.order',
            4 => 'application.rest.rate',
            5 => 'application.rest.offer',
            6 => 'application.rest.package',
            7 => 'application.rest.page',
            8 => 'application.rest.service',
            9 => 'application.rest.category',
            10 => 'application.rest.orderproduct',
            11 => 'application.rest.location',
            12 => 'application.rest.city',
        ),
    ),
    'zf-rest' => array(
        'Application\\V1\\Rest\\Notification\\Controller' => array(
            'listener' => 'Application\\V1\\Rest\\Notification\\NotificationResource',
            'route_name' => 'application.rest.notification',
            'route_identifier_name' => 'user_id',
            'collection_name' => 'notification',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'Application\\V1\\Rest\\Notification\\NotificationEntity',
            'collection_class' => 'Application\\V1\\Rest\\Notification\\NotificationCollection',
            'service_name' => 'notification',
        ),
        'Application\\V1\\Rest\\User\\Controller' => array(
            'listener' => 'Application\\V1\\Rest\\User\\UserResource',
            'route_name' => 'application.rest.user',
            'route_identifier_name' => 'user_id',
            'collection_name' => 'user',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'Application\\V1\\Rest\\User\\UserEntity',
            'collection_class' => 'Application\\V1\\Rest\\User\\UserCollection',
            'service_name' => 'user',
        ),
        'Application\\V1\\Rest\\Address\\Controller' => array(
            'listener' => 'Application\\V1\\Rest\\Address\\AddressResource',
            'route_name' => 'application.rest.address',
            'route_identifier_name' => 'user_id',
            'collection_name' => 'address',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => '100',
            'page_size_param' => null,
            'entity_class' => 'Application\\V1\\Rest\\Address\\AddressEntity',
            'collection_class' => 'Application\\V1\\Rest\\Address\\AddressCollection',
            'service_name' => 'address',
        ),
        'Application\\V1\\Rest\\Order\\Controller' => array(
            'listener' => 'Application\\V1\\Rest\\Order\\OrderResource',
            'route_name' => 'application.rest.order',
            'route_identifier_name' => 'user_id',
            'collection_name' => 'order',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(
                0 => 'user_id',
            ),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'Application\\V1\\Rest\\Order\\OrderEntity',
            'collection_class' => 'Application\\V1\\Rest\\Order\\OrderCollection',
            'service_name' => 'order',
        ),
        'Application\\V1\\Rest\\Rate\\Controller' => array(
            'listener' => 'Application\\V1\\Rest\\Rate\\RateResource',
            'route_name' => 'application.rest.rate',
            'route_identifier_name' => 'rate_id',
            'collection_name' => 'rate',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'Application\\V1\\Rest\\Rate\\RateEntity',
            'collection_class' => 'Application\\V1\\Rest\\Rate\\RateCollection',
            'service_name' => 'rate',
        ),
        'Application\\V1\\Rest\\Offer\\Controller' => array(
            'listener' => 'Application\\V1\\Rest\\Offer\\OfferResource',
            'route_name' => 'application.rest.offer',
            'route_identifier_name' => 'offer_id',
            'collection_name' => 'offer',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'Application\\V1\\Rest\\Offer\\OfferEntity',
            'collection_class' => 'Application\\V1\\Rest\\Offer\\OfferCollection',
            'service_name' => 'offer',
        ),
        'Application\\V1\\Rest\\Package\\Controller' => array(
            'listener' => 'Application\\V1\\Rest\\Package\\PackageResource',
            'route_name' => 'application.rest.package',
            'route_identifier_name' => 'package_id',
            'collection_name' => 'package',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'Application\\V1\\Rest\\Package\\PackageEntity',
            'collection_class' => 'Application\\V1\\Rest\\Package\\PackageCollection',
            'service_name' => 'package',
        ),
        'Application\\V1\\Rest\\Page\\Controller' => array(
            'listener' => 'Application\\V1\\Rest\\Page\\PageResource',
            'route_name' => 'application.rest.page',
            'route_identifier_name' => 'page_key',
            'collection_name' => 'page',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'Application\\V1\\Rest\\Page\\PageEntity',
            'collection_class' => 'Application\\V1\\Rest\\Page\\PageCollection',
            'service_name' => 'page',
        ),
        'Application\\V1\\Rest\\Service\\Controller' => array(
            'listener' => 'Application\\V1\\Rest\\Service\\ServiceResource',
            'route_name' => 'application.rest.service',
            'route_identifier_name' => 'city_id',
            'collection_name' => 'service',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'Application\\V1\\Rest\\Service\\ServiceEntity',
            'collection_class' => 'Application\\V1\\Rest\\Service\\ServiceCollection',
            'service_name' => 'service',
        ),
        'Application\\V1\\Rest\\Category\\Controller' => array(
            'listener' => 'Application\\V1\\Rest\\Category\\CategoryResource',
            'route_name' => 'application.rest.category',
            'route_identifier_name' => 'category_id',
            'collection_name' => 'category',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'Application\\V1\\Rest\\Category\\CategoryEntity',
            'collection_class' => 'Application\\V1\\Rest\\Category\\CategoryCollection',
            'service_name' => 'category',
        ),
        'Application\\V1\\Rest\\Orderproduct\\Controller' => array(
            'listener' => 'Application\\V1\\Rest\\Orderproduct\\OrderproductResource',
            'route_name' => 'application.rest.orderproduct',
            'route_identifier_name' => 'orderproduct_id',
            'collection_name' => 'orderproduct',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'Application\\V1\\Rest\\Orderproduct\\OrderproductEntity',
            'collection_class' => 'Application\\V1\\Rest\\Orderproduct\\OrderproductCollection',
            'service_name' => 'orderproduct',
        ),
        'Application\\V1\\Rest\\Location\\Controller' => array(
            'listener' => 'Application\\V1\\Rest\\Location\\LocationResource',
            'route_name' => 'application.rest.location',
            'route_identifier_name' => 'location_id',
            'collection_name' => 'location',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => '200',
            'page_size_param' => 'size',
            'entity_class' => 'Application\\V1\\Rest\\Location\\LocationEntity',
            'collection_class' => 'Application\\V1\\Rest\\Location\\LocationCollection',
            'service_name' => 'location',
        ),
        'Application\\V1\\Rest\\City\\Controller' => array(
            'listener' => 'Application\\V1\\Rest\\City\\CityResource',
            'route_name' => 'application.rest.city',
            'route_identifier_name' => 'city_id',
            'collection_name' => 'city',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'Application\\V1\\Rest\\City\\CityEntity',
            'collection_class' => 'Application\\V1\\Rest\\City\\CityCollection',
            'service_name' => 'city',
        ),
    ),
    'zf-content-negotiation' => array(
        'controllers' => array(
            'Application\\V1\\Rest\\Notification\\Controller' => 'HalJson',
            'Application\\V1\\Rest\\User\\Controller' => 'HalJson',
            'Application\\V1\\Rest\\Address\\Controller' => 'HalJson',
            'Application\\V1\\Rest\\Order\\Controller' => 'HalJson',
            'Application\\V1\\Rest\\Rate\\Controller' => 'HalJson',
            'Application\\V1\\Rest\\Offer\\Controller' => 'HalJson',
            'Application\\V1\\Rest\\Package\\Controller' => 'HalJson',
            'Application\\V1\\Rest\\Page\\Controller' => 'HalJson',
            'Application\\V1\\Rest\\Service\\Controller' => 'HalJson',
            'Application\\V1\\Rest\\Category\\Controller' => 'HalJson',
            'Application\\V1\\Rest\\Orderproduct\\Controller' => 'HalJson',
            'Application\\V1\\Rest\\Location\\Controller' => 'HalJson',
            'Application\\V1\\Rest\\City\\Controller' => 'HalJson',
        ),
        'accept_whitelist' => array(
            'Application\\V1\\Rest\\Notification\\Controller' => array(
                0 => 'application/vnd.application.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Application\\V1\\Rest\\User\\Controller' => array(
                0 => 'application/vnd.application.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Application\\V1\\Rest\\Address\\Controller' => array(
                0 => 'application/vnd.application.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Application\\V1\\Rest\\Order\\Controller' => array(
                0 => 'application/vnd.application.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Application\\V1\\Rest\\Rate\\Controller' => array(
                0 => 'application/vnd.application.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Application\\V1\\Rest\\Offer\\Controller' => array(
                0 => 'application/vnd.application.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Application\\V1\\Rest\\Package\\Controller' => array(
                0 => 'application/vnd.application.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Application\\V1\\Rest\\Page\\Controller' => array(
                0 => 'application/vnd.application.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Application\\V1\\Rest\\Service\\Controller' => array(
                0 => 'application/vnd.application.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Application\\V1\\Rest\\Category\\Controller' => array(
                0 => 'application/vnd.application.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Application\\V1\\Rest\\Orderproduct\\Controller' => array(
                0 => 'application/vnd.application.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Application\\V1\\Rest\\Location\\Controller' => array(
                0 => 'application/vnd.application.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Application\\V1\\Rest\\City\\Controller' => array(
                0 => 'application/vnd.application.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
        ),
        'content_type_whitelist' => array(
            'Application\\V1\\Rest\\Notification\\Controller' => array(
                0 => 'application/vnd.application.v1+json',
                1 => 'application/json',
            ),
            'Application\\V1\\Rest\\User\\Controller' => array(
                0 => 'application/vnd.application.v1+json',
                1 => 'application/json',
            ),
            'Application\\V1\\Rest\\Address\\Controller' => array(
                0 => 'application/vnd.application.v1+json',
                1 => 'application/json',
            ),
            'Application\\V1\\Rest\\Order\\Controller' => array(
                0 => 'application/vnd.application.v1+json',
                1 => 'application/json',
            ),
            'Application\\V1\\Rest\\Rate\\Controller' => array(
                0 => 'application/vnd.application.v1+json',
                1 => 'application/json',
            ),
            'Application\\V1\\Rest\\Offer\\Controller' => array(
                0 => 'application/vnd.application.v1+json',
                1 => 'application/json',
            ),
            'Application\\V1\\Rest\\Package\\Controller' => array(
                0 => 'application/vnd.application.v1+json',
                1 => 'application/json',
            ),
            'Application\\V1\\Rest\\Page\\Controller' => array(
                0 => 'application/vnd.application.v1+json',
                1 => 'application/json',
            ),
            'Application\\V1\\Rest\\Service\\Controller' => array(
                0 => 'application/vnd.application.v1+json',
                1 => 'application/json',
            ),
            'Application\\V1\\Rest\\Category\\Controller' => array(
                0 => 'application/vnd.application.v1+json',
                1 => 'application/json',
            ),
            'Application\\V1\\Rest\\Orderproduct\\Controller' => array(
                0 => 'application/vnd.application.v1+json',
                1 => 'application/json',
            ),
            'Application\\V1\\Rest\\Location\\Controller' => array(
                0 => 'application/vnd.application.v1+json',
                1 => 'application/json',
            ),
            'Application\\V1\\Rest\\City\\Controller' => array(
                0 => 'application/vnd.application.v1+json',
                1 => 'application/json',
            ),
        ),
    ),
    'zf-hal' => array(
        'metadata_map' => array(
            'Application\\V1\\Rest\\Notification\\NotificationEntity' => array(
                'entity_identifier_name' => 'notif_user_id',
                'route_name' => 'application.rest.notification',
                'route_identifier_name' => 'user_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Application\\V1\\Rest\\Notification\\NotificationCollection' => array(
                'entity_identifier_name' => 'notif_user_id',
                'route_name' => 'application.rest.notification',
                'route_identifier_name' => 'user_id',
                'is_collection' => true,
            ),
            'Application\\V1\\Rest\\User\\UserEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'application.rest.user',
                'route_identifier_name' => 'user_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Application\\V1\\Rest\\User\\UserCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'application.rest.user',
                'route_identifier_name' => 'user_id',
                'is_collection' => true,
            ),
            'Application\\V1\\Rest\\Address\\AddressEntity' => array(
                'entity_identifier_name' => 'addr_id',
                'route_name' => 'application.rest.address',
                'route_identifier_name' => 'user_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Application\\V1\\Rest\\Address\\AddressCollection' => array(
                'entity_identifier_name' => 'addr_id',
                'route_name' => 'application.rest.address',
                'route_identifier_name' => 'user_id',
                'is_collection' => true,
            ),
            'Application\\V1\\Rest\\Order\\OrderEntity' => array(
                'entity_identifier_name' => 'order_user_id',
                'route_name' => 'application.rest.order',
                'route_identifier_name' => 'user_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Application\\V1\\Rest\\Order\\OrderCollection' => array(
                'entity_identifier_name' => 'order_user_id',
                'route_name' => 'application.rest.order',
                'route_identifier_name' => 'user_id',
                'is_collection' => true,
            ),
            'Application\\V1\\Rest\\Rate\\RateEntity' => array(
                'entity_identifier_name' => 'item_price_id',
                'route_name' => 'application.rest.rate',
                'route_identifier_name' => 'rate_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Application\\V1\\Rest\\Rate\\RateCollection' => array(
                'entity_identifier_name' => 'item_price_id',
                'route_name' => 'application.rest.rate',
                'route_identifier_name' => 'rate_id',
                'is_collection' => true,
            ),
            'Application\\V1\\Rest\\Offer\\OfferEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'application.rest.offer',
                'route_identifier_name' => 'offer_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Application\\V1\\Rest\\Offer\\OfferCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'application.rest.offer',
                'route_identifier_name' => 'offer_id',
                'is_collection' => true,
            ),
            'Application\\V1\\Rest\\Package\\PackageEntity' => array(
                'entity_identifier_name' => 'package_id',
                'route_name' => 'application.rest.package',
                'route_identifier_name' => 'package_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Application\\V1\\Rest\\Package\\PackageCollection' => array(
                'entity_identifier_name' => 'package_id',
                'route_name' => 'application.rest.package',
                'route_identifier_name' => 'package_id',
                'is_collection' => true,
            ),
            'Application\\V1\\Rest\\Page\\PageEntity' => array(
                'entity_identifier_name' => 'page_key',
                'route_name' => 'application.rest.page',
                'route_identifier_name' => 'page_key',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Application\\V1\\Rest\\Page\\PageCollection' => array(
                'entity_identifier_name' => 'page_key',
                'route_name' => 'application.rest.page',
                'route_identifier_name' => 'page_key',
                'is_collection' => true,
            ),
            'Application\\V1\\Rest\\Service\\ServiceEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'application.rest.service',
                'route_identifier_name' => 'city_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Application\\V1\\Rest\\Service\\ServiceCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'application.rest.service',
                'route_identifier_name' => 'city_id',
                'is_collection' => true,
            ),
            'Application\\V1\\Rest\\Category\\CategoryEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'application.rest.category',
                'route_identifier_name' => 'category_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Application\\V1\\Rest\\Category\\CategoryCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'application.rest.category',
                'route_identifier_name' => 'category_id',
                'is_collection' => true,
            ),
            'Application\\V1\\Rest\\Orderproduct\\OrderproductEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'application.rest.orderproduct',
                'route_identifier_name' => 'orderproduct_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Application\\V1\\Rest\\Orderproduct\\OrderproductCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'application.rest.orderproduct',
                'route_identifier_name' => 'orderproduct_id',
                'is_collection' => true,
            ),
            'Application\\V1\\Rest\\Location\\LocationEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'application.rest.location',
                'route_identifier_name' => 'location_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Application\\V1\\Rest\\Location\\LocationCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'application.rest.location',
                'route_identifier_name' => 'location_id',
                'is_collection' => true,
            ),
            'Application\\V1\\Rest\\City\\CityEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'application.rest.city',
                'route_identifier_name' => 'city_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Application\\V1\\Rest\\City\\CityCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'application.rest.city',
                'route_identifier_name' => 'city_id',
                'is_collection' => true,
            ),
        ),
    ),
    'zf-content-validation' => array(
        'Application\\V1\\Rest\\Notification\\Controller' => array(
            'input_filter' => 'Application\\V1\\Rest\\Notification\\Validator',
        ),
        'Application\\V1\\Rest\\User\\Controller' => array(
            'input_filter' => 'Application\\V1\\Rest\\User\\Validator',
        ),
        'Application\\V1\\Rest\\Address\\Controller' => array(
            'input_filter' => 'Application\\V1\\Rest\\Address\\Validator',
        ),
        'Application\\V1\\Rest\\Order\\Controller' => array(
            'input_filter' => 'Application\\V1\\Rest\\Order\\Validator',
        ),
        'Application\\V1\\Rest\\Rate\\Controller' => array(
            'input_filter' => 'Application\\V1\\Rest\\Rate\\Validator',
        ),
    ),
    'input_filter_specs' => array(
        'Application\\V1\\Rest\\Notification\\Validator' => array(
            0 => array(
                'required' => true,
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\NotEmpty',
                        'options' => array(
                            'message' => 'Valid user id needed',
                        ),
                    ),
                ),
                'filters' => array(),
                'name' => 'notif_user_id',
                'description' => 'User Id against which notification is saved',
                'error_message' => 'Please provide user id of user against which notification data is passed',
            ),
            1 => array(
                'required' => true,
                'validators' => array(),
                'filters' => array(),
                'name' => 'notif_message',
                'description' => 'Notification text',
                'error_message' => 'Please provide notificaiton message',
            ),
            2 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'notif_read',
                'description' => 'Field to show whether notification was read or unread',
                'allow_empty' => true,
                'continue_if_empty' => true,
            ),
        ),
        'Application\\V1\\Rest\\User\\Validator' => array(
            0 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'user_fname',
                'description' => 'First name of user',
            ),
            1 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'user_lname',
            ),
            2 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'user_number',
            ),
            3 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'user_email',
                'description' => 'Email address',
            ),
        ),
        'Application\\V1\\Rest\\Address\\Validator' => array(
            0 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'addr_user_id',
            ),
            1 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'addr_label',
            ),
            2 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'addr_default',
                'description' => 'Default is home address',
            ),
            3 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'addr_flat_no',
            ),
            4 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'addr_street',
            ),
            5 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'addr_city',
            ),
            6 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'addr_pincode',
            ),
            7 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'addr_name',
            ),
        ),
        'Application\\V1\\Rest\\Order\\Validator' => array(
            0 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'order_user_id',
            ),
            1 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'order_delivery_type',
            ),
            2 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'order_first_name',
            ),
            3 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'order_last_name',
            ),
            4 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'order_user_email',
            ),
            5 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'order_address',
            ),
            6 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'order_address_additional',
            ),
            7 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'order_city',
            ),
            8 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'order_pincode',
            ),
            9 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'order_pickup',
            ),
            10 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'order_delivery',
            ),
            11 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'order_amount',
            ),
            12 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'order_payment_type',
            ),
            13 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'order_service_type',
            ),
            14 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'order_payment_status',
            ),
            15 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'order_status',
            ),
        ),
        'Application\\V1\\Rest\\Rate\\Validator' => array(
            0 => array(
                'required' => true,
                'validators' => array(),
                'filters' => array(),
                'name' => 'item_id',
                'error_message' => 'Item id is required',
            ),
        ),
    ),
);
