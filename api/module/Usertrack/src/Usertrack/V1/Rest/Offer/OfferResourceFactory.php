<?php
namespace Usertrack\V1\Rest\Offer;

class OfferResourceFactory
{
    public function __invoke($services)
    {
        return new OfferResource($services->get('Usertrack\Mapper\UsertrackMapper'));
    }
}
