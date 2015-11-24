<?php

/*
 * @author - Ankit Sharma
 * @created - 30 August 2015 6:23 PM
 *
 */
require "constants.php";
require "connection.php";

require('Texttolocal.php');

//error_reporting(E_ALL);

//$data = file_get_contents("php://input");
//$data = $_GET['mobile_no'];

$code = mt_rand("1237", "9998");
$retArr = array("response_id"=>'0',"verification_code"=>$code);
//if (isset($data) && !empty($data->mobile_no)) {
if (isset($_GET['mobile_no']) && !empty($_GET['mobile_no'])) {
    $data = $_GET['mobile_no'];
    $toMobile = array('91'.$data);
    $msgApp = new Texttolocal('laundrywala', 'cleanlaundry');
    
    $message = $msgApp->sendMessage(
            $toMobile, // Text this number
//            "$code. Please do not share this number"
            "Dear $code, Welcome to LaundryWala. Do login to our website www.laundrywala.co.in for more details. Look forward to being of service to you. Regards, LaundryWala"
    );
//    echo '<pre>'; print_r($message); exit('Macro die');
    $retArr['response_id'] = '1';
}

echo json_encode($retArr);
?>
