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

        if(!isset(static::$session)) {
            $cookieName = 'session_cookie';
            $cookieValue = ($_COOKIE($cookieName) ?? null);

            if ($cookieValue === null) {
                return false;
            }//end if

            $request = new \nba\shared\messaging\frontend\SessionValidateRequest($cookieValue, 'session_request');
            $rabbitClient = new \nba\rabbit\RabbitMQClient(__DIR__.'/../../../rabbit/host.ini', "Authentication");
            $response = $rabbitClient->send_request(json_encode($request), 'application/json');
            $responseData = json_decode($response, true);
            if($responseData['type'] === 'session_response') {
                if ($responseData->getResult()) {
                    $session = new \nba\shared\Session(
                        $responseData->getToken(),
                        $responseData->getExpiration(),
                        $responseData->getUserID(),
                        $responseData->getEmail()
                    );

                    if(isset($session)) {
                        static::$session = $session;
                        return $session;

                    }//end if
                }//end if
            } else {

            return false;
            
            }//end if-else

        } else {
        
            return static::$session;
        
        }//end if-else
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
        $host = [];
        $host = NULL;
        $rabbitClient = new \nba\rabbit\RabbitMQClient($host, "testServer");
        $response = $rabbitClient->send_request(json_encode($request), 'php-serialized');
        $responseData = json_decode($response, true);
        if($responseData['type'] === 'login_response') {
            if ($responseData->getResult()) {
                $session = new \nba\shared\Session(
                    $responseData->getToken(),
                    $responseData->getExpiration(),
                    $responseData->getUserID(),
                    $responseData->getEmail()
                );

                if (isset($session)){
                    setcookie(
                        $cookieName,
                        $session->getSessionToken(),
                        $session->getExpirationTimestamp(),
                        '/',
                        $_SERVER['SERVER_NAME'],
                    );
                    static::$session = $session;
                    return $session;
                }
            }
        } else {

        return false;
        
        }
    }
}
?>