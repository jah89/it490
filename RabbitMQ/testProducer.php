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

// Connection with RabbitMQ
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

// Get the query data 
$requestData = json_decode(file_get_contents('php://input'), true);

$action = isset($_POST['action']) ? $_POST['action'] : (isset($requestData['action']) ? $requestData['action'] : 'login');
$username = isset($_POST['username']) ? $_POST['username'] : (isset($requestData['username']) ? $requestData['username'] : 'testUser');
$password = isset($_POST['password']) ? $_POST['password'] : (isset($requestData['password']) ? $requestData['password'] : 'testPass');

if ($action === null || $username === null || $password === null) {
    echo "No valid data provided!" . PHP_EOL;
    exit;
}

// Create message 
$data = json_encode([
    'action' => $action, 
    'username' => $username, 
    'password' => $password
]);
$msg = new AMQPMessage($data, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);

//message the exchange with ini file
$channel->basic_publish($msg, $authConfig['EXCHANGE'], 'login');

echo 'Message sent to RabbitMQ!' . PHP_EOL;

$channel->close();
$connection->close();
?>
