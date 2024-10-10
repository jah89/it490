<?php
/**
 * Contains response from DB processor for a login request.
 */
namespace nba\shared\messaging\frontend;

use JsonSerializable;

/**
 * Response from DB processor to login request.
 */
abstract class LoginResponse implements JsonSerializable
{

    /**
     * True for success, false for fail.
     *
     * @var boolean $result
     */
    private bool $result;

    /**
     * The type of request, login response.
     *
     * @param string $type
     */
    private string $type = 'login_response';

        /**
     * User's username.
     *
     * @var string $email is User's email.
     */
    private string $email;

    /**
     * User's session token.
     *
     * @var string $sessionToken is the user's session token.
     */
    private string $sessionToken;

    /**
     * Session expiration time using Unix epoch timestamp.
     *
     * @var string $expirationTimestamp is the timestamp of session expiration.
     */
    private string $expirationTimestamp;

    /**
     * User's ID.
     *
     * @var string $UID is the ID of user.
     */
    private string $userID;


    /**
     * Creates a new login response.
     *
     * @param boolean              $result  True if succesfully logged in, otherwise false.
     * @param Session $session The session object, defaults to null.
     */
    public function __construct(bool $result, string $type = 'login_response', string $email, string $sessionToken, int $expirationTimestamp, int $userID)
    {
        $this->result  = $result;
        $this->type = $type;
        $this->email = $email;
        $this->sessionToken = $sessionToken;
        $this->expirationTimestamp = $expirationTimestamp;
        $this->userID = $userID;

    }

    public function jsonSerialize(): mixed
    {
        return [
            'type'=> $this->type,
            'result'=>$this->result,
            'email'=>$this->email,
            'token'=>$this->sessionToken,
            'timestamp'=> $this->expirationTimestamp,
            'userID'=> $this->userID
        ];
    }
    //start of getters
    /**
     * Gets login result.
     *
     * @return boolean True if success.
     */
    public function getResult()
    {
        return $this->result;

    }

    /**
     * Gets user's username.
     *
     * @return string Username.
     */
    public function getEmail()
    {
        return $this->email;

    }

    /**
     * Gets session token.
     *
     * @return string Session token.
     */
    public function getSessionToken()
    {
        return $this->sessionToken;

    }

    /**
     * Gets session expiration timestamp.
     *
     * @return integer Session expiration timestamp.
     */
    public function getExpirationTimestamp()
    {
        return $this->expirationTimestamp;

    }

    /**
     * Gets the user ID.
     *
     * @return string UID.
     */
    public function getUserId()
    {
        return $this->userID;

    }//end getUserId()

}//end class