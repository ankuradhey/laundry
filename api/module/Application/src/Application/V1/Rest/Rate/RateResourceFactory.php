<?php
namespace Application\V1\Rest\Rate;

class RateResourceFactory
{
    public function __invoke($services)
    {
        return new RateResource($services);
    }
}
