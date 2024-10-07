<?php
require_once(__DIR__.'/../../frontend/rabbit/RabbitMQServer.php');
require_once('MessageProcessor.php');

// Define the callback function that will handle incoming messages
function messageCallback($payload) {
    // Instantiate the message processor
    $messageProcessor = new MessageProcessor();
    
    // Process the message based on its type (inside the MessageProcessor class)
    $messageProcessor->process($payload);
    
    // Optionally, return a response if needed
    return $messageProcessor->getResponse();
}

// Instantiate the RabbitMQServer class, passing in the host configuration
$rabbitMQServer = new RabbitMQServer(server config here);

// Start processing requests and pass the callback function for processing messages
$rabbitMQServer->process_requests('messageCallback');
