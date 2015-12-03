<?php
namespace Application\V1\Rest\Category;

class CategoryResourceFactory
{
    public function __invoke($services)
    {
        return new CategoryResource($services->get('Application\V1\Rest\Category\CategoryMapper'));
    }
}
