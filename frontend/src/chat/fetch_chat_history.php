<?php
namespace nba\frontend\src\chat;

$rabbitClient = new \nba\rabbit\RabbitMQClient(__DIR__."/../../rabbit/host.ini", "CHAT"); // Instantiate your RabbitMQ client
$userID = \nba\shared\Session->getUserID();
$requestMessage = json_encode(['type' => 'get_chat_history', 'userID']); // Define the request

// Send the request to RabbitMQ and wait for the response
$response = $rabbitClient->send_request($requestMessage, 'application/json');

// Decode the response (assume it's JSON encoded chat history)
$chatHistory = json_decode($response, true);

// Send the response back to the frontend
header('Content-Type: application/json');
echo json_encode($chatHistory); // Send the chat history to the JavaScript frontend
?>