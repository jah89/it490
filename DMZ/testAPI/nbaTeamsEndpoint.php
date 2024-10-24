#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "https://v2.nba.api-sports.io/teams?conference=West", // added conference to get all teams on Eat conference can be changed to West as well (change team id to get specific teams)
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
	/// Wrap the response in an associative array with a type
	
    $message = [
        'type' => 'api_team_data_request',
        'data' => $response // Decode response to associative array
		//'data' => json_decode($response, true) // Decode response to associative array

    ];
	
/*
	$message = [
		'type' => 'api_game_data_request',
		'data' => [
			'id' => 123,
			'home_team_id' => 1,
			'visitor_team_id' => 2,
			'date' => '2024-10-19T19:00:00Z',
			'home_team_score' => 100,
			'visitor_team_score' => 98,
		]
	];
	*/

    // Publish the message to RabbitMQ
	echo(print_r($message, true));
    $client->publish(json_encode($message)); // Send as JSON string


}