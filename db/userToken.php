<?php


require_once '../frontend/shared/Session.php';
//require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require '../frontend/shared/messaging/frontend/LoginResponse.php';
require_once 'rabbitMQLib.inc';
require '../frontend/shared/messaging/frontend/ConcreteLoginResponse.php'; 

use nba\shared\Session;
use nba\shared\messaging\frontend\ConcreteLoginResponse;


$token = uniqid();
$timestamp = time() + (3 * 3600);
$session = new Session($token, $timestamp, "bob");
$loginResponse = new ConcreteLoginResponse(true, $session);

//$loginResponse = new LoginResponse(true, null);

/*
consuming LoginRequest Object of Rabbit

then use getEMail function to use in session instance  Ex. session = new Session(getEmail(), $token, $timestamp);

then info gets inserted into db

then session object created and LoginResponse object created and send to rabbit which you consume
use publish

*/



// Initialize the RabbitMQ client
$client = new rabbitMQClient("testRabbitMQ.ini","testServer"); 

//$message = array("action" => "test", "message" => "Hello RabbitMQ);

$message = $loginResponse;

// Sends message using publish method and uses seralize to manage the data
$response = $client->publish(serialize($message));

/*
// Display the response
echo "Received response: \n";
print_r($response);

*/

?>