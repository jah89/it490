<?php
namespace nba\rabbit;
//require_once('get_host_info.php');

/**
 * Professor's client class. The client that sends messages to rabbitMQ.
 */
class RabbitMQClient
{
	private $machine = "";
	public  $BROKER_HOST;
	private $BROKER_PORT;
	private $USER;
	private $PASSWORD;
	private $VHOST;
	private $serverExchange;
	//private $clientExchange;
	private $queue;
	//private $queue_prefix;
	private $routing_key = '*';
	private $responses = array();
	private $exchange_type = "classic";
	private $auto_delete;

	function __construct($machine, $server)
	{
		$this->machine 		 = \nba\rabbit\RabbitHostInfo::getHostInfo(array($machine));
		$this->BROKER_HOST   = $this->machine[$server]["BROKER_HOST"];
		$this->BROKER_PORT   = $this->machine[$server]["BROKER_PORT"];
		$this->USER     	 = $this->machine[$server]["USER"];
		$this->PASSWORD 	 = $this->machine[$server]["PASSWORD"];
		$this->VHOST 		 = $this->machine[$server]["VHOST"];
		if (isset( $this->machine[$server]["EXCHANGE_TYPE"]))
		{
			$this->exchange_type = $this->machine[$server]["EXCHANGE_TYPE"];
		}
		if (isset( $this->machine[$server]["AUTO_DELETE"]))
		{
			$this->auto_delete = $this->machine[$server]["AUTO_DELETE"];
		}
		$this->serverExchange = $this->machine[$server]["SERVER_EXCHANGE"];
		//$this->clientExchange = $this->machine[$server]["CLIENT_EXCHANGE"];
		$this->queue 		  = $this->machine[$server]["QUEUE"];
		//$this->queue_prefix   = $this->machine[$server]['QUEUE_PREFIX'];
		$this->responses = array();
	}

	function process_response($response, $response_queue)
	{
		$uid = $response->getCorrelationId();
		if (!isset($this->responses[$uid]))
		{
		  echo  "unknown uid\n";
		  return true;
		}
    $response_queue->ack($response->getDeliveryTag());
		$body = $response->getBody();
		$payload = json_decode($body, true);
		if (!(isset($payload)))
		{
			$payload = "[empty response]";
		}
		$this->responses[$uid] = $payload;
		return false;
	}

    /**
     * Sends a request to RabbitMq.
     *
     * @param mixed $message Message to send.
     * @param string $contentType Message MIME type.
     *
     * @return mixed Message response.
     * @throws \Exception Exception on timeout.
     */
	function send_request($message, string $contentType)
	{
		$uid = uniqid();

		try
		{
		$params = array();
		$params['host'] = $this->BROKER_HOST;
		$params['port'] = $this->BROKER_PORT;
		$params['login'] = $this->USER;
		$params['password'] = $this->PASSWORD;
		$params['vhost'] = $this->VHOST;

		$conn = new \AMQPConnection($params);
		$conn->connect();

	  	$channel = new \AMQPChannel($conn);

	  	$exchange = new \AMQPExchange($channel);
      	$exchange->setName($this->serverExchange);
      	$exchange->setType($this->exchange_type);

      	$callback_queue = new \AMQPQueue($channel);
      	$callback_queue->setName($this->queue.".".$uid);
		$callback_queue->setFlags(\AMQP_AUTODELETE);
      	$callback_queue->declare();
		$callback_queue->bind($exchange->getName(),$this->routing_key.".".$uid);

			// $this->response_queue = new AMQPQueue($channel);
			// $this->conn_queue->setName($this->queue);
			// $this->conn_queue->bind($exchange->getName(),$this->routing_key);

			if (empty($this->serverExchange)) {
				die("Exchange name is empty. Please set a valid exchange name.\n");
			}
		$exchange->publish(
			$message,
			$this->routing_key,
			\AMQP_NOPARAM,
			array('content_type'=> $contentType, 
			'reply_to'=>$callback_queue->getName(),
			'correlation_id'=>$uid)
			);

      		$this->responses[$uid] = "waiting";
			$callback_queue->consume(array($this,'process_response'));

			$response = $this->responses[$uid];
			//unset($this->responses[$uid]);
			return $response;
		}
		catch(\Exception $e)
		{
			die("failed to send message to exchange: ". $e->getMessage()."\n");
		}
	}

	/**
	*@brief send a one-way message to the server.  These are
	* auto-acknowledged and give no response.
	*
	* @param message the body of the request.  This must make sense to the
	*server
	 */
	function oneway_publish($message)
	{
		//$json_message = json_encode($message);
		try
		{
      $params = array();
      $params['host'] = $this->BROKER_HOST;
      $params['port'] = $this->BROKER_PORT;
      $params['login'] = $this->USER;
      $params['password'] = $this->PASSWORD;
      $params['vhost'] = $this->VHOST;
			$conn = new \AMQPConnection($params);
			$conn->connect();
			$channel = new \AMQPChannel($conn);
			$exchange = new \AMQPExchange($channel);
      $exchange->setName($this->serverExchange);
      $exchange->setType($this->exchange_type);
			$this->conn_queue = new \AMQPQueue($channel);
			$this->conn_queue->setName($this->queue);
			$this->conn_queue->bind($exchange->getName(),$this->routing_key);
			return $exchange->publish($message,$this->routing_key,\AMQP_NOPARAM,['content_type'=>'php-serialized']);
		}
		catch(\Exception $e)
		{
			die("failed to send message to exchange: ". $e->getMessage()."\n");
		}
	}
}
?>
