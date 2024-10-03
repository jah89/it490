<?php
/**
 * Class that handles sessions on the frontend.
 * Houses the logic behind login/logout attempts.
 */
namespace NBA\Frontend;

/**
 * Class to handle session-related messaging.
 */
abstract class SessionHandler {

    /**
     * The session object obtained from DB-side logic
     */
    private static ?\NBA\Frontend\Messaging\Session $session;

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

            $request = new \NBA\Frontend\Messaging\SessionValidateRequest($cookieValue);
            $rabbitClient = new \NBA\Frontend\rabbitMQClient(__DIR__.'/../../../rabbit/host.ini', "Authentication");
            $response = $rabbitClient->send_request($request);

            if($response instanceof \NBA\Frontend\Messaging\SessionValidateResponse) {
                if ($response->getResult()) {
                    $session = $response->getSession();

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
        $request = new \NBA\Frontend\Messaging\LoginRequest($email, $hashedPassword);
        $rabbitClient = new \NBA\Frontend\rabbitMQClient(__DIR__.'/../../../rabbit/host.ini', "Authentication");
        $response = $rabbitClient->send_request(serialize($request, 'application/php-serialized' ));

        if($response instanceof \NBA\Frontend\Messaging\LoginResponse) {
            if ($response->getResult()){
                $session = $response->getSession();
                if (isset($session)){
                    setcookie(
                        $cookieName,
                        $session->getSessionToken(),
                        $session->getExpirationTimestamp().
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