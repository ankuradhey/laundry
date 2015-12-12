<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require "constants.php";
require "connection.php";

$data = file_get_contents("php://input");
$data = json_decode($data);
//$code = mt_rand("1237", "9998");
$retArr = array("response_id" => '0');

if (isset($data) && $data->isVerified){
	
    $mobile = $data->mobile_no;

    //check if user number already registered
    $res = mysql_query('select * from users where user_number = "' . mysql_real_escape_string($mobile) . '" ');
    if (!$res)
        $retArr['error'] = mysql_error();
    else {
        $res = mysql_fetch_array($res);
        if (count($res) && !empty($res)) {
            $retArr['user_id'] = $res['user_id'];
            $retArr['first_name'] = $res['user_fname'];
            $retArr['last_name'] = $res['user_lname'];
            $retArr['city'] = $res['user_city'];
            $retArr['response_id'] = '1';
        } else {
            $res = mysql_query("insert into users(user_number) values('" . mysql_real_escape_string($mobile) . "');  ");
            if ($res) {
                //GET USER ID
                $res = mysql_query('select * from users where user_number = "' . mysql_real_escape_string($mobile) . '" ');
                
                if (!$res)
                    $retArr['error'] = mysql_error();
                else{
                    $res = mysql_fetch_array($res);
                    if(count($res)){
                        $retArr['user_id'] = $res[0]['user_id'];
                        $retArr['first_name'] = $res['user_fname'];
                        $retArr['last_name'] = $res['user_lname'];
                        $retArr['city'] = $res['user_city'];
                        $retArr['response_id'] = '1';
                    }else{
                        $retArr['error'] = 'user not found';
                    }
                }
            } else {
                $retArr['error'] = mysql_error();
            }
        }
    }
}
echo json_encode($retArr);
?>