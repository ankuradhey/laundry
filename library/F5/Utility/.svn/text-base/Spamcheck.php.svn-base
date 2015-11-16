<?php

class F5_Utility_Spamcheck extends Zend_Mail_Transport_Abstract
{
    public $email;
    public $option = 'long';    
    private $_values;

    protected function _sendmail() 
    {
    	$this->email = $this->header . Zend_Mime::LINEEND . $this->body;
    }    
        
    private function validate() 
    {
        if ($this->email == "" || $this->option == "") {
            die('Required data missing.');
        } else {
            $this->_values = array('email' => $this->email, 'options' => $this->option);
        }
    }
    
    public function checkSpam()
    {
        $this->validate();
        // encode the data ready to be sent
        $json_data = json_encode($this->_values);
        // set the headers in an array to be used by curl
        $http_headers = array("Accept: application/json", "Content-Type: application/json");
        
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,"http://spamcheck.postmarkapp.com/filter");
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); // return the data
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"POST"); // we're doing a POST
        curl_setopt($ch,CURLOPT_POSTFIELDS,$json_data); // send the data in the json array
        curl_setopt($ch,CURLOPT_HTTPHEADER, $http_headers); // add the headers
        $result = curl_exec($ch); // run the curl
        if (curl_error($ch) != "") {
            die('Curl reported this error: '.curl_error($ch));
        }
        curl_close($ch); // close curl
        $result = json_decode($result,true); // decode the json data contained in the result
        $result['email'] = $this->email;
                    
        return $result;
    }
}
