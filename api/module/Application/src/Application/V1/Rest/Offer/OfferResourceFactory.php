<?php
namespace Application\V1\Rest\Offer;

class OfferResourceFactory
{
    public function __invoke($services)
    {
        return new OfferResource($services->get('Application\V1\Rest\Offer\OfferMapper'));
    }
}
