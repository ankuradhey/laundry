<?php
namespace Application\V1\Rest\City;

class CityResourceFactory
{
    public function __invoke($services)
    {
        return new CityResource($services->get('Application\V1\Rest\Location\LocationMapper'));
    }
}
