<?php
namespace nba\frontend\src\api;

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['uname']) || !isset($data['msg'])) {
    error_log(print_r(json_encode(['success' => false, 'error' => 'Invalid message data']),true));
    exit();
}
/**
 * Sends chat message to database for storage
 *
 * @param string $uname user who sent message
 * @param string $msg text of message
 * @return mixed $response response from backend
 */
function sendMessage($uname, $msg) {
    $rabbitClient = new \nba\rabbit\RabbitMQClient(__DIR__.'/../../../rabbit/host.ini', "Chat");
    
    $request = [
        'type' => 'chat_message',
        'uname' => $uname,
        'msg' => $msg,
        'timestamp' => date('Y-m-d H:i:s')
    ];

    $response = $rabbitClient->send_request(json_encode($request), 'application/json');
    return json_decode($response, true);
}
//TODO: handle result from DB side, may need changes here
$result = sendMessage($data['uname'], $data['msg']);
header('Content-Type: application/json');
echo json_encode(['success' => $result['result']]);
