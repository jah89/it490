<?php
/**
 * Contains message of session requests for frontend.
 *
 */

 namespace nba\shared\messaging\frontend;

/**
 * A request from the frontend for a session operation. Other classes are based on this.
 */
abstract class SessionRequest extends \nba\shared\messaging\frontend\LoginRequest {

    /**
     * Session Token from user.
     *
     * @var string $sessionToken Session token
     */
    private string $sessionToken;


    /**
     * Request type, session request.
     *
     * @var string $type The request type.
     */
    private string $type = 'session_request';

    /**
     * Builds a new session request.
     *
     * @param string $sessionToken session token.
     * */
    public function __construct(string $sessionToken, string $type)
    {
        $this->sessionToken = $sessionToken;
        $this->type         = $type;

    }


    /**
     * Gets the user's session token.
     *
     * @return string User's session token.
     */
    public function getSessionToken()
    {
        return $this->sessionToken;

    }


}