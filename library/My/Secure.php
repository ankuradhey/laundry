<?php

class My_Secure {
    private $secret_key;
    private $secret_iv;
    public function __construct() {
        $this->secret_key = "This is a test project key";
    }
    public function encode($string)
    {
        $output = false;
        $hash = $this->hash();
        $string = $hash*$string;
        $output = base64_encode($string);
        return $output;
    }
    public function decode($encrypted_string)
    {
        $output = false;
        $output = base64_decode($encrypted_string);
        $hash = $this->hash();
        $output = $output/$hash;
        return $output;
    }
    public function hash()
    {
        $ascii = NULL;
        for ($i = 0; $i < strlen($this->secret_key); $i++) 
        { 
            $ascii += ord($this->secret_key[$i]); 
        }
        $length = strlen($this->secret_key);
        $ascii = (double) $ascii.".".$length;
        return $ascii;
    }
}