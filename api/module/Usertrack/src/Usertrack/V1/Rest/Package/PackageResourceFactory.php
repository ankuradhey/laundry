<?php
namespace Usertrack\V1\Rest\Package;

class PackageResourceFactory
{
    public function __invoke($services)
    {
        return new PackageResource($services->get('Usertrack\Mapper\UsertrackMapper'));
    }
}
