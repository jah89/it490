<?php
require 'Session.inc.php';


<<<<<<< HEAD
echo uniqid() . "\n";
echo time() ."\n";

function makeToken($username)
{
$token = uniqid();
$timestamp = time();
$session = new Session($username, $token, $timestamp);
}
=======
//echo uniqid() . "\n";
//echo time() ."\n";


$token = uniqid();
$timestamp = time();
$session = new Session($username, $token, $timestamp);





>>>>>>> 75c652daa502738f5397828af55081aa5f1e8a99

?>