#!/usr/bin/php
<?php
require_once(__DIR__ . '/path.inc');
require_once(__DIR__ . '/get_host_info.inc');
require_once(__DIR__ . '/rabbitMQLib.inc');
require __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

// Parse the ini file 
$config = parse_ini_file(__DIR__ . '/testRabbitMQ.ini', true);
$authConfig = $config['Authentication'];

$connection = new AMQPStreamConnection(
    $authConfig['BROKER_HOST'],
    $authConfig['BROKER_PORT'],
    $authConfig['USER'],
    $authConfig['PASSWORD'],
    $authConfig['VHOST']
);
$channel = $connection->channel();

// Declare the queue
$channel->queue_declare($authConfig['QUEUE'], false, true, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

// Callback function
$callback = function ($msg) {
    $data = json_decode($msg->body, true);

    // Process the message based on the action field
    switch ($data['action']) {
        case 'login':
            handleLogin($data);
            break;

        case 'register':
            handleRegistration($data);
            break;

        default:
            error_log("Unknown action: " . $data['action']);
    }

    
    $msg->ack();
};

// handle login action
function handleLogin($data) {
    error_log("Processing login for user: " . $data['username']);

}

// handle registration action
function handleRegistration($data) {
    error_log("Processing registration for user: " . $data['username']);
    // Implement your registration logic
}

//consumer to listen to the queue
$channel->basic_consume($authConfig['QUEUE'], '', false, true, false, false, $callback);

// Keep the script running 
while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();
?>
