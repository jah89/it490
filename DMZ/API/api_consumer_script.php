#!/usr/bin/php

<?php
include(__DIR__.'/RabbitMQServer.php');
require_once('APIMessageProcessor.php');

// Define the callback function that will handle incoming messages
function messageCallback($request) {

    echo("Request message received: ". print_r($request,true));

    // Instantiate the API message processor
    $apiMessageProcessor = new APIMessageProcessor(); // Change to new class name
    
    // Process the message based on its type (inside the APIMessageProcessor class)
    $apiMessageProcessor->call_processor($request);
    
    // Optionally, return a response if needed
    return $apiMessageProcessor->getResponse(); // Assuming getResponse() exists
}

// Instantiate the RabbitMQServer class, passing in the host configuration
$rabbitMQServer = new RabbitMQServer(__DIR__.'/testRabbitMQ.ini','API');

// Start processing requests and pass the callback function for processing messages
$rabbitMQServer->process_requests('messageCallback');

//$callback = function($message) use ($rabbitMQServer) {
    // Process message
    //echo "Message received: " . $message->body . "\n";

    // Acknowledge the message
    //$rabbitMQServer->getChannel()->basic_ack($message->delivery_info['delivery_tag']);
//};

//$rabbitMQServer->process_requests($callback);
?>