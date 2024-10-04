#!/usr/bin/php
<?php

require_once('rabbitMQLib.inc'); 

$host = '172.30.17.239';
$user = 'miz';
$password = 'teamfantasy';
$dbname = 'nba';

// Connect to the MySQL database
$mydb = new mysqli($host, $user, $password, $dbname);

if ($mydb->connect_error) {
    echo "Failed to connect to database: " . $mydb->connect_error . PHP_EOL;
    exit(0);
}
echo "Successfully connected to database." . PHP_EOL;

// Function to process the received message
function processMessage($msg) {
    global $mydb;

    $email = $mydb->real_escape_string($msg['email']);
    $hashed_password = $mydb->real_escape_string($msg['hashed_password']);

    // Insert the message data into the users table
    $sql = "INSERT INTO users (email, hashed_password) VALUES ('$email', '$hashed_password')";

    if ($mydb->query($sql) === TRUE) {
        echo "Record successfully inserted for email: " . $email . PHP_EOL;
    } else {
        echo "Error: " . $sql . " - " . $mydb->error . PHP_EOL;
    }
}

// Create the RabbitMQ server object to listen for messages
$rabbitMQServer = new rabbitMQServer("testRabbitMQ.ini", "testServer");

// Pass the 'processMessage' function as a callback to handle each message
$rabbitMQServer->process_requests('processMessage');

$mydb->close();
?>
