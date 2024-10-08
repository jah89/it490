#!/usr/bin/php

<?php
include(__DIR__.'/../RabbitMQServer.php');
require_once('MessageProcessor.php');

// Define the callback function that will handle incoming messages
function messageCallback($request) {
    // Instantiate the message processor
    $messageProcessor = new MessageProcessor();
    
    // Process the message based on its type (inside the MessageProcessor class)
    $messageProcessor->call_processor($request);
    
    // Optionally, return a response if needed
    return $messageProcessor->getResponse();
}

// Instantiate the RabbitMQServer class, passing in the host configuration
$rabbitMQServer = new rabbitMQServer(__DIR__.'/../host.ini','testServer');

// Start processing requests and pass the callback function for processing messages
$rabbitMQServer->process_requests('messageCallback');
