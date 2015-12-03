<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'Deliveryboy\\V1\\Rest\\Authenticate\\AuthenticateResource' => 'Deliveryboy\\V1\\Rest\\Authenticate\\AuthenticateResourceFactory',
            'Deliveryboy\\V1\\Rest\\Order\\OrderResource' => 'Deliveryboy\\V1\\Rest\\Order\\OrderResourceFactory',
            'Deliveryboy\\V1\\Rest\\Orderproducts\\OrderproductsResource' => 'Deliveryboy\\V1\\Rest\\Orderproducts\\OrderproductsResourceFactory',
        ),
    ),
    'router' => array(
        'routes' => array(
            'deliveryboy.rest.authenticate' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/deliveryboys/authenticate[/:user_id]',
                    'defaults' => array(
                        'controller' => 'Deliveryboy\\V1\\Rest\\Authenticate\\Controller',
                    ),
                ),
            ),
            'deliveryboy.rest.order' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/deliveryboys/orders[/:delboy_id[/status[/:status_type]]]',
                    'defaults' => array(
                        'controller' => 'Deliveryboy\\V1\\Rest\\Order\\Controller',
                    ),
                ),
            ),
            'deliveryboy.rest.orderproducts' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/deliveryboys/orderproducts[/:orderproducts_id]',
                    'defaults' => array(
                        'controller' => 'Deliveryboy\\V1\\Rest\\Orderproducts\\Controller',
                    ),
                ),
            ),
        ),
    ),
    'zf-versioning' => array(
        'uri' => array(
            0 => 'deliveryboy.rest.authenticate',
            1 => 'deliveryboy.rest.order',
            2 => 'deliveryboy.rest.orderproducts',
        ),
    ),
    'zf-rest' => array(
        'Deliveryboy\\V1\\Rest\\Authenticate\\Controller' => array(
            'listener' => 'Deliveryboy\\V1\\Rest\\Authenticate\\AuthenticateResource',
            'route_name' => 'deliveryboy.rest.authenticate',
            'route_identifier_name' => 'user_id',
            'collection_name' => 'authenticate',
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
            'entity_class' => 'Deliveryboy\\Mapper\\DeliveryboyEntity',
            'collection_class' => 'Deliveryboy\\V1\\Rest\\Authenticate\\AuthenticateCollection',
            'service_name' => 'authenticate',
        ),
        'Deliveryboy\\V1\\Rest\\Order\\Controller' => array(
            'listener' => 'Deliveryboy\\V1\\Rest\\Order\\OrderResource',
            'route_name' => 'deliveryboy.rest.order',
            'route_identifier_name' => 'delboy_id',
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
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'Application\\V1\\Rest\\Order\\OrderEntity',
            'collection_class' => 'Application\\V1\\Rest\\Order\\OrderCollection',
            'service_name' => 'order',
        ),
        'Deliveryboy\\V1\\Rest\\Orderproducts\\Controller' => array(
            'listener' => 'Deliveryboy\\V1\\Rest\\Orderproducts\\OrderproductsResource',
            'route_name' => 'deliveryboy.rest.orderproducts',
            'route_identifier_name' => 'orderproducts_id',
            'collection_name' => 'orderproducts',
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
            'entity_class' => 'Deliveryboy\\V1\\Rest\\Orderproducts\\OrderproductsEntity',
            'collection_class' => 'Deliveryboy\\V1\\Rest\\Orderproducts\\OrderproductsCollection',
            'service_name' => 'orderproducts',
        ),
    ),
    'zf-content-negotiation' => array(
        'controllers' => array(
            'Deliveryboy\\V1\\Rest\\Authenticate\\Controller' => 'HalJson',
            'Deliveryboy\\V1\\Rest\\Order\\Controller' => 'HalJson',
            'Deliveryboy\\V1\\Rest\\Orderproducts\\Controller' => 'HalJson',
        ),
        'accept_whitelist' => array(
            'Deliveryboy\\V1\\Rest\\Authenticate\\Controller' => array(
                0 => 'application/vnd.deliveryboy.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Deliveryboy\\V1\\Rest\\Order\\Controller' => array(
                0 => 'application/vnd.deliveryboy.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Deliveryboy\\V1\\Rest\\Orderproducts\\Controller' => array(
                0 => 'application/vnd.deliveryboy.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
        ),
        'content_type_whitelist' => array(
            'Deliveryboy\\V1\\Rest\\Authenticate\\Controller' => array(
                0 => 'application/vnd.deliveryboy.v1+json',
                1 => 'application/json',
            ),
            'Deliveryboy\\V1\\Rest\\Order\\Controller' => array(
                0 => 'application/vnd.deliveryboy.v1+json',
                1 => 'application/json',
            ),
            'Deliveryboy\\V1\\Rest\\Orderproducts\\Controller' => array(
                0 => 'application/vnd.deliveryboy.v1+json',
                1 => 'application/json',
            ),
        ),
    ),
    'zf-hal' => array(
        'metadata_map' => array(
            'Deliveryboy\\V1\\Rest\\Authenticate\\AuthenticateEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'deliveryboy.rest.authenticate',
                'route_identifier_name' => 'authenticate_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Deliveryboy\\V1\\Rest\\Authenticate\\AuthenticateCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'deliveryboy.rest.authenticate',
                'route_identifier_name' => 'authenticate_id',
                'is_collection' => true,
            ),
            'Deliveryboy\\Mapper\\DeliveryboyEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'deliveryboy.rest.authenticate',
                'route_identifier_name' => 'user_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Deliveryboy\\V1\\Rest\\Order\\OrderEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'deliveryboy.rest.order',
                'route_identifier_name' => 'order_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Deliveryboy\\V1\\Rest\\Order\\OrderCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'deliveryboy.rest.order',
                'route_identifier_name' => 'order_id',
                'is_collection' => true,
            ),
            'Application\\V1\\Rest\\Order\\OrderEntity' => array(
                'entity_identifier_name' => 'delboy_id',
                'route_name' => 'deliveryboy.rest.order',
                'route_identifier_name' => 'delboy_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Application\\V1\\Rest\\Order\\OrderCollection' => array(
                'entity_identifier_name' => 'delboy_id',
                'route_name' => 'deliveryboy.rest.order',
                'route_identifier_name' => 'delboy_id',
                'is_collection' => true,
            ),
            'Deliveryboy\\V1\\Rest\\Orderproducts\\OrderproductsEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'deliveryboy.rest.orderproducts',
                'route_identifier_name' => 'orderproducts_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Deliveryboy\\V1\\Rest\\Orderproducts\\OrderproductsCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'deliveryboy.rest.orderproducts',
                'route_identifier_name' => 'orderproducts_id',
                'is_collection' => true,
            ),
        ),
    ),
    'zf-content-validation' => array(
        'Deliveryboy\\V1\\Rest\\Authenticate\\Controller' => array(
            'input_filter' => 'Deliveryboy\\V1\\Rest\\Authenticate\\Validator',
        ),
        'Deliveryboy\\V1\\Rest\\Orderproducts\\Controller' => array(
            'input_filter' => 'Deliveryboy\\V1\\Rest\\Orderproducts\\Validator',
        ),
    ),
    'input_filter_specs' => array(
        'Deliveryboy\\V1\\Rest\\Authenticate\\Validator' => array(
            0 => array(
                'required' => true,
                'validators' => array(),
                'filters' => array(),
                'name' => 'delboy_username',
                'error_message' => 'Username required',
                'description' => 'Username of delivery Boy',
            ),
            1 => array(
                'required' => true,
                'validators' => array(),
                'filters' => array(),
                'name' => 'delboy_password',
                'description' => 'Password of delivery boy',
                'error_message' => 'Password is required',
            ),
        ),
        'Deliveryboy\\V1\\Rest\\Orderproducts\\Validator' => array(
            0 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'order_id',
            ),
            1 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'order_product_name',
            ),
            2 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'order_item_id',
            ),
            3 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'order_offer_id',
            ),
            4 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'order_type',
            ),
            5 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'package_id',
            ),
            6 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'unit_price',
            ),
            7 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'total_price',
            ),
            8 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'quantity',
            ),
        ),
    ),
);
