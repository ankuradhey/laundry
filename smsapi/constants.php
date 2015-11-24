<?php
/*
 * @author - Ankit Sharma
 * @created - 30 August 2015 6:23 PM
 *
 */
    if ($_SERVER["REMOTE_ADDR"] == "127.0.0.1")
    {
        define("BASE_URL", "http://localhost/work/smsapi");
    }
    else
    {
        define("BASE_URL", "http://blgindia.com/vas");
    }

    define("TTLUSERNAME","chetan@laundrywala.co.in");
    define("TTLHASH","557c69004c17bdbd598dba27fcb9a23ae7127232");
    define("TTLSENDER","Laundrywala");
    
?>