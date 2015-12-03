<?php
namespace Application\V1\Rest\Location;

class LocationResourceFactory
{
    public function __invoke($services)
    {
        return new LocationResource($services->get('Application\V1\Rest\Location\LocationMapper'));
    }
}
