<?php  
require_once(__DIR__ . '/path.inc');
require_once(__DIR__ . '/get_host_info.inc');
require_once(__DIR__ . '/rabbitMQLib.inc');
require __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Parse the ini file to get the RabbitMQ configuration
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

// Declare queue based on ini file
$channel->queue_declare($authConfig['QUEUE'], false, true, false, false);

$action = 'login'; // Hardcoding testing
$username = 'testUser'; 
$password = 'testPass'; 

// Create message 
$data = json_encode([
    'action' => $action, 
    'username' => $username, 
    'password' => $password
]);
$msg = new AMQPMessage($data, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);

// msg exchange with the routing key 'login'
$channel->basic_publish($msg, $authConfig['EXCHANGE'], 'login');

echo 'Message sent to RabbitMQ!' . PHP_EOL;

$channel->close();
$connection->close();
?>
