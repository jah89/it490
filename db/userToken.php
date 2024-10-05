<?php
require 'Session.inc.php';


//echo uniqid() . "\n";
//echo time() ."\n";


$token = uniqid();
$timestamp = time();
$session = new Session($username, $token, $timestamp);






?>