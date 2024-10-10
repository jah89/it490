#!/usr/bin/php
<?php

require_once('rabbitMQLib.inc');
require_once('userToken.php');

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

    // Check if this is a login request or registration
    if ($msg['action'] == 'register') {
        // Check if email already exists
        $check_sql = "SELECT * FROM users WHERE email = '$email'";
        $check_result = $mydb->query($check_sql);

        if ($check_result && $check_result->num_rows > 0) {
            echo "Registration failed: An account with this email already exists. Try logging in." . PHP_EOL;
            return; // Exit the function to stop further processing
        }

        // Insert the message data into the users table for registration
        $sql = "INSERT INTO users (email, hashed_password) VALUES ('$email', '$hashed_password')";
        
        if ($mydb->query($sql) === TRUE) {
            echo "User registered successfully with email: " . $email . PHP_EOL;

            // Generate session token and timestamp using values from userToken.php
            global $token, $timestamp, $username; // Include $username to create a Session object

            // Create a new Session object
            $session = new Session($username, $token, $timestamp);

            // Insert session data into sessions table
            $session_sql = "INSERT INTO sessions (session_token, email, timestamp) 
                            VALUES ('$session->token', '$email', '" . date('Y-m-d H:i:s', $session->timestamp) . "')";
            
            if ($mydb->query($session_sql) === TRUE) {
                echo "Session created for user: " . $email . PHP_EOL;
                echo "Session Token: " . $session->token . PHP_EOL;
            } else {
                echo "Error creating session: " . $session_sql . " - " . $mydb->error . PHP_EOL;
            }
        } else {
            echo "Error inserting user: " . $sql . " - " . $mydb->error . PHP_EOL;
        }
    } elseif ($msg['action'] == 'login') {
        // Check if the email exists
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $mydb->query($sql);

        if ($result && $result->num_rows > 0) {
            // Check if the hashed password matches
            $user = $result->fetch_assoc();
            if ($user['hashed_password'] === $hashed_password) {
                echo "Login successful for user: " . $email . PHP_EOL;

            // Generate session token and timestamp using values from userToken.php
            global $token, $timestamp, $username;

            // Create a new Session object
            $session = new Session($username, $token, $timestamp);

            // Insert session data into sessions table
            $session_sql = "INSERT INTO sessions (session_token, email, timestamp) 
                            VALUES ('$session->token', '$email', '" . date('Y-m-d H:i:s', $session->timestamp) . "')";
            
            if ($mydb->query($session_sql) === TRUE) {
                echo "Session created for user: " . $email . PHP_EOL;
                echo "Session Token: " . $session->token . PHP_EOL;
            } else {
                echo "Error creating session: " . $session_sql . " - " . $mydb->error . PHP_EOL;
            }
        } else {
            echo "Login failed for user: " . $email . PHP_EOL;
        }
    }
    }
}


    


// Create the RabbitMQ server object to listen for messages
$rabbitMQServer = new rabbitMQServer("testRabbitMQ.ini", "Authentication");

// Pass the 'processMessage' function as a callback to handle each message
$rabbitMQServer->process_requests('processMessage');

$mydb->close();
?>
