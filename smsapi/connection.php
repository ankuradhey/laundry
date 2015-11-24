<?php
/*
 * @author - Ankit Sharma
 * @created - 30 August 2015 6:23 PM
 *
 */

  $db = mysql_connect('166.62.8.8', 'laundryUser', 'Laundry@123');
  
  if(!$db) exit("Database not linked");
  
  $resource = mysql_select_db('laundryUser');
  
  session_start();
  error_reporting(0);
?>