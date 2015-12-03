<?php
namespace Application\V1\Rest\Service;

class ServiceResourceFactory
{
    public function __invoke($services)
    {
        return new ServiceResource($services->get('Application\V1\Rest\Service\ServiceMapper'));
    }
}
