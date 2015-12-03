<?php
namespace Deliveryboy\V1\Rest\Order;

class OrderResourceFactory
{
    public function __invoke($services)
    {
        return new OrderResource($services->get('Deliveryboy\Mapper\DeliveryboyMapper'));
    }
}
