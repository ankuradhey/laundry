<?php
namespace Application\V1\Rest\User;

class UserEntity
{
    public $user_id;
    public $user_fname;
    public $user_lname;
    public $user_number;
    
    public function getArrayCopy() {
        return array(
            'user_id' => $this->user_id,
            'user_fname' => $this->user_fname,
            'user_lname' => $this->user_lname,
            'user_number' => $this->user_number,
        );
    }

    public function exchangeArray(array $array) {
        $this->user_id = $array['user_id'];
        $this->user_fname = $array['user_fname'];
        $this->user_lname = $array['user_lname'];
        $this->user_number = $array['user_number'];
    }

}
