<?php
namespace Application\V1\Rest\Notification;

class NotificationResourceFactory
{
    public function __invoke($services)
    {
        return new NotificationResource($services->get('Application\V1\Rest\Notification\NotificationMapper'));
    }
}
