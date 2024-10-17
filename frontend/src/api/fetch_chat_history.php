<?php
namespace nba\frontend\src\api;

function fetchChatHistory() {
    $rabbitClient = new \nba\rabbit\RabbitMQClient(__DIR__.'/../../../rabbit/host.ini', "Chat");
    
    $request = ['type' => 'chat_history'];
    $response = $rabbitClient->send_request(json_encode($request), 'application/json');
    
    return json_decode($response, true);
}

header('Content-Type: application/json');
echo json_encode(fetchChatHistory());
?>