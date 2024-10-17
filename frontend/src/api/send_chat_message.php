<?php
namespace nba\frontend\src\chat;

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['uname']) || !isset($data['msg'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid message data']);
    exit();
}

function sendMessage($uname, $msg) {
    $rabbitClient = new \nba\rabbit\RabbitMQClient(__DIR__.'/../../../rabbit/host.ini', "Chat");
    
    $request = [
        'type' => 'sendMessage',
        'uname' => $uname,
        'msg' => $msg,
        'timestamp' => date('Y-m-d H:i:s')
    ];

    $response = $rabbitClient->send_request(json_encode($request), 'application/json');
    return json_decode($response, true);
}

$result = sendMessage($data['uname'], $data['msg']);
header('Content-Type: application/json');
echo json_encode(['success' => $result['success']]);
