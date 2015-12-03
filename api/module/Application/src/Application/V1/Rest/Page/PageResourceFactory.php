<?php
namespace Application\V1\Rest\Page;

class PageResourceFactory
{
    public function __invoke($services)
    {
        return new PageResource($services->get('Application\V1\Rest\Page\PageMapper'));
    }
}
