<?php

namespace Application\V1\Rest\Notification;

class NotificationEntity {

    public $notif_id;
    public $notif_user_id;
    public $notif_message;
    public $notif_read;
    public $notif_status;
    public $notif_added_time;

    public function getArrayCopy() {
        return array(
            'notif_id' => $this->notif_id,
            'notif_user_id' => $this->notif_user_id,
            'notif_message' => $this->notif_message,
            'notif_read' => $this->notif_read,
            'notif_status' => $this->notif_status,
            'notif_added_time' => $this->notif_added_time
        );
    }

    public function exchangeArray(array $array) {
        $this->notif_id = $array['notif_id'];
        $this->notif_user_id = $array['notif_user_id'];
        $this->notif_message = $array['notif_message'];
        $this->notif_read = $array['notif_read'];
        $this->notif_status = $array['notif_status'];
        $this->notif_added_time = $array['notif_added_time'];
    }

}
