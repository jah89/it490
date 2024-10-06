<?php
require 'Session.inc.php';


echo uniqid() . "\n";
echo time() ."\n";

function makeToken($username)
{
$token = uniqid();
$timestamp = time();
$session = new Session($username, $token, $timestamp);
}

?>