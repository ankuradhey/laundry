<?php

namespace Application\Model;

class Notification
{
        
    /**
     */ public $notif_id;
        public $notif_user_id;
        public $notif_message;     
        public $notif_read;     
        public $notif_status;     
        public $notif_added_time;
        
        public function exchangeArray($data)
        { 
                $this->notif_id           = (isset($data['notif_id'])) ? $data['notif_id'] : null;
                $this->notif_user_id      = (isset($data['notif_user_id'])) ? $data['notif_user_id'] : null;
                $this->notif_message     = (isset($data['notif_message'])) ? $data['notif_message'] : null;
                $this->notif_read     = (isset($data['notif_read'])) ? $data['notif_read'] : null;
                $this->notif_status     = (isset($data['notif_read'])) ? $data['notif_read'] : null;
		$this->notif_added_time        = (isset($data['notif_added_time'])) ? $data['notif_added_time'] : null;
        }
        
         public function getArrayCopy()
        {
         return get_object_vars($this);
        }
        
}
?>
