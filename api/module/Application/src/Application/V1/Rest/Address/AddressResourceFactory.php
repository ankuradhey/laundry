<?php
namespace Application\V1\Rest\Address;

class AddressResourceFactory
{
    public function __invoke($services)
    {
        return new AddressResource($services->get('Application\V1\Rest\Address\AddressMapper'));
    }
}
