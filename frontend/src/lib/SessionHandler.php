<?php
/**
 * Class that handles sessions on the frontend.
 * Houses the logic behind login/logout attempts.
 */
namespace nba\src\lib;
//require_once __DIR__.'/../../../shared/messaging/frontend/LoginRequest.php';
/**
 * Class to handle session-related messaging.
 */
abstract class SessionHandler {

    /**
     * The session object obtained from DB-side logic
     */
    private static \nba\shared\Session $session;

    /** Function to get session object. Checks server-side first,
     * then sends request to DB via Rabbit.
     * 
     * @return false OR $session object
     */
    public static function getSession(){

        //If no session exists, try to retrieve one and put in a cookie
        if(!isset(static::$session)) {
            $cookieName = 'session_cookie';

        //check for session cookie being set
        if (!isset($_COOKIE[$cookieName])) {
            return false; // No cookie found, return false
        }

        $cookieValue = ($_COOKIE($cookieName) ?? null);
        $expirationTimestamp = $_COOKIE[$cookieName . '_expires'] ?? null;

        if ($cookieValue === null || $expirationTimestamp === null) {
            return false;
        }//end if

        $request = new \nba\shared\messaging\frontend\SessionValidateRequest('validate_request', $cookieValue, $expirationTimestamp);
        $rabbitClient = new \nba\rabbit\RabbitMQClient(__DIR__.'/../../../rabbit/host.ini', "Authentication");
        $response = $rabbitClient->send_request(json_encode($request), 'application/json');
        $responseData = json_decode($response, true);
        $responsePayload = $responseData['payload'];
        if($responseData['type'] === 'login_response' && $responsePayload['result'] == true) {
            static::$session = new \nba\shared\Session(
                $responsePayload['token'],
                $responsePayload['expiration'],
                //$responsePayload['userID'],
                $responsePayload['email']
            );
                return static::$session;
            } else {
                return false;
            }
        //return session if already set
        return static::$session;
        }
}

    /**
     * Sends login request to DB-side via RabbitMQ. 
     * Response is session info and is used to set cookie.
     * This is main logic that handles login form submission via RabbitMQ.
     * 
     * @param string $email
     * @param string $hashedPassword
     * 
     * @return false or Session object
     */
    public static function login(string $email, string $hashedPassword) {

        $cookieName = 'session_cookie';
        $request = new \nba\shared\messaging\frontend\LoginRequest($email, $hashedPassword, 'login_request');
        //error_log(print_r($request,true));
        $rabbitClient = new \nba\rabbit\RabbitMQClient(__DIR__.'/../../rabbit/host.ini', "Authentication");
        $response = $rabbitClient->send_request(json_encode($request), 'application/json');

        if($response['type'] === 'login_response' &&  $response['result'] == true) {
            static::$session = new \nba\shared\Session(
                $response['session_token'],
                $response['expiration_timestamp'],
                //$response['userID'],
                $response['email']
            );
            //error_log('session setting issue' . print_r(static::$session, true));

        } else {
            return false;
        }
                if (isset(static::$session)){
                    error_log("session is set   ". print_r(static::$session,true));
                    if (headers_sent()) {
                        error_log('Headers already sent.');
                    } else {
                        error_log('setting cookie');
                    setcookie(
                        $cookieName,
                        static::$session->getSessionToken(),
                        static::$session->getExpirationTimestamp(),
                        '/',
                        $_SERVER['SERVER_NAME'],
                    );
                    error_log('cookie was set  '. print_r($_COOKIE, true));
                }
                    return static::$session;
                } else {
                    return false;
        }
    }
}
?>