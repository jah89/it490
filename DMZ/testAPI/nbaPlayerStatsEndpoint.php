#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini","API");

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "https://v2.nba.api-sports.io/players/statistics?id=20&season=2023", //need to specify season to get player stats
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "GET",
	CURLOPT_HTTPHEADER => [
		"x-rapidapi-host: api-nba-v1.p.rapidapi.com",
		"x-rapidapi-key: c0cb78e69959e338dce6adbd219977b2"
	],
]);


$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
	echo "cURL Error #:" . $err;
} else {
	
    $message = [
        'type' => 'api_player_stats_request',
        'data' => $response 

    ];
	

    // Publish the message to RabbitMQ
	echo(print_r($message, true)); //debug statement to see what data looks like before being sent
    $client->publish(json_encode($message)); // Send as JSON string


}