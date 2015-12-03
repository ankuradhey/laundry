<?php
namespace Application\V1\Rest\Orderproduct;

class OrderproductResourceFactory
{
    public function __invoke($services)
    {
        return new OrderproductResource();
    }
}
