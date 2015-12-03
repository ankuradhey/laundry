<?php
namespace Application\V1\Rest\Package;

class PackageResourceFactory
{
    public function __invoke($services)
    {
        return new PackageResource($services->get('Application\V1\Rest\Package\PackageMapper'));
    }
}
