<?php
require 'Session.inc.php';


//echo uniqid() . "\n";
//echo time() ."\n";


$token = uniqid();
$timestamp = time() + (3 * 3600);
$session = new Session($token, $timestamp, "bob");
$loginResponse = new ConcreteLoginResponse(true, $session);

//$loginResponse = new LoginResponse(true, null);

/*
consuming LoginRequest Object of Rabbit

then use getEMail function to use in session instance  Ex. session = new Session(getEmail(), $token, $timestamp);






?>