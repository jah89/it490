<?php

require_once('get_host_info.inc');

class RabbitMQServer
{
	private array $machine;
	public  string $BROKER_HOST;
	private int $BROKER_PORT;
	private string $USER;
	private string $PASSWORD;
	private string $VHOST;

	//created and changed names to strings for less confusion
	private string $clientExchangeName;
	private string $serverQueueName;
	private $routing_key = '*';
	private $exchange_type = "direct";
	private $serverQueue;
	private $callback;
	private $auto_delete = false;
	private $responses = array();


	function __construct($machine, $server = "rabbitMQ")
	{
		$this->machine 			    = getHostInfo(array($machine));
		$this->BROKER_HOST  	    = $this->machine[$server]["BROKER_HOST"];
		$this->BROKER_PORT  	    = $this->machine[$server]["BROKER_PORT"];
		$this->USER     		    = $this->machine[$server]["USER"];
		$this->PASSWORD 		    = $this->machine[$server]["PASSWORD"];
		$this->VHOST 			    = $this->machine[$server]["VHOST"];
		$this->clientExchangeName   = $this->machine[$server]['SERVER_EXCHANGE']; 
		if (isset( $this->machine[$server]["EXCHANGE_TYPE"]))
		{
			$this->exchange_type = $this->machine[$server]["EXCHANGE_TYPE"];
		}
		// if (isset( $this->machine[$server]["AUTO_DELETE"]))
		// {
		// 	$this->auto_delete = $this->machine[$server]["AUTO_DELETE"];
		// }
		$this->serverQueueName = $this->machine[$server]["QUEUE"];
	}
	/**
	 * Function to process message from queue.
	 *
	 * @param AMQPEnvelope $msg The message being sent.
	 * @return boolean success or fail
	 */
	function process_message($msg)
	{
		if ($msg->getRoutingKey() !== "*")
    {
      return true;
    }
	
	// send the ack to clear the item from the queue
    $this->serverQueue->ack($msg->getDeliveryTag());
		try
		{
			if ($msg->getReplyTo())
			{
				// message wants a response, process the request
				$body = $msg->getBody();
				$request = json_decode($body, true);
				error_log("request:   " . json_encode($request));
				if (isset($this->callback))
				{
					$response = call_user_func($this->callback, $request);
				}

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
      $exchange->setName($this->clientExchangeName);
      $exchange->setType($this->exchange_type);
	  $exchange->setFlags(\AMQP_DURABLE);

			$serverQueue = new \AMQPQueue($channel);
			$serverQueue->setName($msg->getReplyTo());
			$replykey = $this->routing_key.".response";
			$serverQueue->bind($exchange->getName(),$replykey);
			$exchange->publish(
				json_encode($response),
				$replykey,
				\AMQP_NOPARAM,
				array('correlation_id'=>$msg->getCorrelationId())
			);

				return;
			} else {
				//if no response required send an ack automatically,
				$body = $msg->getBody();
				$request = json_decode($body, true);
                if (isset($this->callback)) {
                    call_user_func($this->callback, $request);
                }
				echo "processed one-way message\n";
            }
		}
		catch(\Exception $e)
		{
			// ampq throws exception if get fails...
            echo "error: rabbitMQServer: process_message: exception caught: ".$e;
		}
	} //end of process_message

	/**
	 * Function to connect to server and begin processing requests.
	 *
	 * @param callable? $callback Callback func when a request comes in.
	 * @return void
	 */
	function process_requests($callback)
	{
		try
		{
			$this->callback 	 = $callback;
            $params 			 = array();
            $params['host'] 	 = $this->BROKER_HOST;
            $params['port'] 	 = $this->BROKER_PORT;
            $params['login'] 	 = $this->USER;
            $params['password']  = $this->PASSWORD;
            $params['vhost'] 	 = $this->VHOST;
			
			//add heartbeat to keep queue active
			$params['heartbeat'] = 60;

			$conn 				 = new \AMQPConnection($params);
			$conn->connect();

			$channel = new \AMQPChannel($conn);

			$exchange = new \AMQPExchange($channel);
            $exchange->setName($this->clientExchangeName);
            $exchange->setType($this->exchange_type);
			$exchange->setFlags(\AMQP_DURABLE);
            $exchange->declareExchange();

			$this->serverQueue = new \AMQPQueue($channel);
			$this->serverQueue->setName($this->serverQueueName);
            $this->serverQueue->setFlags(\AMQP_DURABLE);  // Ensure that the queue is declared as durable
            $this->serverQueue->declareQueue();  // Now declare the queue
			$this->serverQueue->bind($exchange->getName(),$this->routing_key);
			$this->serverQueue->consume(array($this,'process_message'));

			// Loop as long as the channel has callbacks registered
			while (count($channel->callbacks))
			{
				$channel->wait();
			}
		}
		catch (\Exception $e)
		{
			trigger_error("Failed to start request processor: ".$e,E_USER_ERROR); 
		}
	}
}
