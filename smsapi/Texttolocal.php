<?php

class Texttolocal{
    protected $username;
    protected $password;
    function __construct($username, $password){
        $this->username = $username;
        $this->password = $password;
    }
    
//    public function sendMessage($sender, Array $numbers, $message, $isTest = 'false'){
//        $message = urlencode($message);
//        $numbers = implode(",",$numbers);
//	$data = "username=".$this->username."&hash=".$this->hash."&message=".$message."&sender=".$sender."&numbers=".$numbers."&test=".$test;
//	$ch = curl_init('http://api.textlocal.in/send/?');
//	curl_setopt($ch, CURLOPT_POST, true);
//	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//	$result = curl_exec($ch); // This is the result from the API
//	curl_close($ch);
//        return $result;
//    }

    public function sendMessage(Array $numbers, $message){
		
        $message = urlencode($message);
        $message = trim(str_replace(' ', '%20', $message));
        $numbers = implode(",",$numbers);
		$data = "user=".$this->username."&pwd=".$this->password."&msg=".$message."&sid=LAWALA&to=".$numbers."&fl=0&gwid=2";
//	$ch = curl_init('http://api.textlocal.in/send/?');
        $url = 'http://login.smsgatewayhub.com/smsapi/pushsms.aspx?'.$data;
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec($ch); // This is the result from the API
		curl_close($ch);
    }
}
	
?>