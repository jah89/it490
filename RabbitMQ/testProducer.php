#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

//connection details from the ini file
$config = parse_ini_file('testRabbitMQ.ini', true);
$authConfig = $config['Authentication'];

// Establish connection to RabbitMQ 
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


$messageBody = json_encode(['type' => 'login', 'username' => 'testUser', 'password' => 'testPassword']);
$msg = new AMQPMessage($messageBody, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);

//  message to the exchange
$channel->basic_publish($msg, $authConfig['EXCHANGE'], '');


echo 'Message sent to RabbitMQ!' . PHP_EOL;

$channel->close();
$connection->close();
?>
