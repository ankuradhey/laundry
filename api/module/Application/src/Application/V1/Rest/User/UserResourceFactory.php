<?php
namespace Application\V1\Rest\User;

class UserResourceFactory
{
    public function __invoke($services)
    {
        return new UserResource($services->get('\Application\V1\Rest\User\UserMapper'));
    }
}
