<?php
namespace Deliveryboy\V1\Rest\Authenticate;

class AuthenticateResourceFactory
{
    public function __invoke($services)
    {
        return new AuthenticateResource($services->get('Deliveryboy\Mapper\DeliveryboyMapper'));
    }
}
