<?php
namespace Deliveryboy\V1\Rest\Orderproducts;

class OrderproductsResourceFactory
{
    public function __invoke($services)
    {
        return new OrderproductsResource($services->get('Deliveryboy\Mapper\DeliveryboyMapper'));
    }
}
