<?php 
require_once(__DIR__ . '/path.inc');
require_once(__DIR__ . '/get_host_info.inc');
require_once(__DIR__ . '/rabbitMQLib.inc');
require '/vendor/autoload.php'; 
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Parse the ini file to get the RabbitMQ configuration
$config = parse_ini_file(__DIR__ . '/testRabbitMQ.ini', true);
$authConfig = $config['Authentication'];

//connection with RabbitMQ
$connection = new AMQPStreamConnection(
    $authConfig['BROKER_HOST'],
    $authConfig['BROKER_PORT'],
    $authConfig['USER'],
    $authConfig['PASSWORD'],
    $authConfig['VHOST']
);

$channel = $connection->channel();

// Declare queue based on ini file
$channel->queue_declare($authConfig['QUEUE'], false, true, false, false);

$requestData = json_decode(file_get_contents('php://input'), true);
// Create message 
$data = json_encode(['action' => 'query_data', 'query' => $_POST['query']]);
$msg = new AMQPMessage($data, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);

// message to the exchange with ini file
$channel->basic_publish($msg, $authConfig['EXCHANGE'], 'login');

echo 'Message sent to RabbitMQ!' . PHP_EOL;

$channel->close();
$connection->close();
?>
