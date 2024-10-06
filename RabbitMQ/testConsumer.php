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

// authentication queue based on the ini file
$channel->queue_declare($authConfig['QUEUE'], false, true, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
    $data = json_decode($msg->body, true);
    if ($data['action'] === 'login') {
        handleLogin($data);
    } else {
        error_log("Unknown action: " . $data['action']);
    }
    $msg->ack();
};

function handleLogin($data) {
    error_log("Processing login for user: " . $data['username']);
}

$channel->basic_consume($authConfig['QUEUE'], '', false, true, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();
?>
