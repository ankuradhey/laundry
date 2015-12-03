<?php
namespace Application\V1\Rest\Order;

class OrderResourceFactory
{
    public function __invoke($services)
    {
        return new OrderResource($services->get('Application\V1\Rest\Order\OrderMapper'));
    }
}
