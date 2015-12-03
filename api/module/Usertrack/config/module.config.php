<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'Usertrack\\V1\\Rest\\Package\\PackageResource' => 'Usertrack\\V1\\Rest\\Package\\PackageResourceFactory',
            'Usertrack\\V1\\Rest\\Offer\\OfferResource' => 'Usertrack\\V1\\Rest\\Offer\\OfferResourceFactory',
        ),
    ),
    'router' => array(
        'routes' => array(
            'usertrack.rest.package' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/usertrack/packages[/:user_id]',
                    'defaults' => array(
                        'controller' => 'Usertrack\\V1\\Rest\\Package\\Controller',
                    ),
                ),
            ),
            'usertrack.rest.offer' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/usertrack/offers[/:user_id]',
                    'defaults' => array(
                        'controller' => 'Usertrack\\V1\\Rest\\Offer\\Controller',
                    ),
                ),
            ),
        ),
    ),
    'zf-versioning' => array(
        'uri' => array(
            0 => 'usertrack.rest.package',
            1 => 'usertrack.rest.offer',
        ),
    ),
    'zf-rest' => array(
        'Usertrack\\V1\\Rest\\Package\\Controller' => array(
            'listener' => 'Usertrack\\V1\\Rest\\Package\\PackageResource',
            'route_name' => 'usertrack.rest.package',
            'route_identifier_name' => 'user_id',
            'collection_name' => 'Usertrack\\Mapper\\UsertrackMapper',
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
            'entity_class' => 'Usertrack\\Entity\\UsertrackEntity',
            'collection_class' => 'Usertrack\\V1\\Rest\\Package\\PackageCollection',
            'service_name' => 'package',
        ),
        'Usertrack\\V1\\Rest\\Offer\\Controller' => array(
            'listener' => 'Usertrack\\V1\\Rest\\Offer\\OfferResource',
            'route_name' => 'usertrack.rest.offer',
            'route_identifier_name' => 'user_id',
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
            'entity_class' => 'Usertrack\\Entity\\UsertrackEntity',
            'collection_class' => 'Usertrack\\V1\\Rest\\Offer\\OfferCollection',
            'service_name' => 'offer',
        ),
    ),
    'zf-content-negotiation' => array(
        'controllers' => array(
            'Usertrack\\V1\\Rest\\Package\\Controller' => 'HalJson',
            'Usertrack\\V1\\Rest\\Offer\\Controller' => 'HalJson',
        ),
        'accept_whitelist' => array(
            'Usertrack\\V1\\Rest\\Package\\Controller' => array(
                0 => 'application/vnd.usertrack.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Usertrack\\V1\\Rest\\Offer\\Controller' => array(
                0 => 'application/vnd.usertrack.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
        ),
        'content_type_whitelist' => array(
            'Usertrack\\V1\\Rest\\Package\\Controller' => array(
                0 => 'application/vnd.usertrack.v1+json',
                1 => 'application/json',
            ),
            'Usertrack\\V1\\Rest\\Offer\\Controller' => array(
                0 => 'application/vnd.usertrack.v1+json',
                1 => 'application/json',
            ),
        ),
    ),
    'zf-hal' => array(
        'metadata_map' => array(
            'Usertrack\\V1\\Rest\\Package\\PackageEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'usertrack.rest.package',
                'route_identifier_name' => 'user_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Usertrack\\V1\\Rest\\Package\\PackageCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'usertrack.rest.package',
                'route_identifier_name' => 'user_id',
                'is_collection' => true,
            ),
            'Usertrack\\Entity\\UsertrackEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'usertrack.rest.package',
                'route_identifier_name' => 'user_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            '\\Usertrack\\Entity\\UsertrackEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'usertrack.rest.package',
                'route_identifier_name' => 'user_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Usertrack\\V1\\Rest\\Offer\\OfferEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'usertrack.rest.offer',
                'route_identifier_name' => 'offer_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Usertrack\\V1\\Rest\\Offer\\OfferCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'usertrack.rest.offer',
                'route_identifier_name' => 'user_id',
                'is_collection' => true,
            ),
        ),
    ),
);
