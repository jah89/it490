<?php
/**
* User session object.
*/
namespace nba\shared;
/**
* A user's session.
*/
class Session {

    /**
     * User's username.
     *
     * @var string $email is User's email/username.
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
     * @var string $userID is the ID of user.
     */
    //private string $userID;

    /**
    * Constructor to create a new session.
    *
    * @param string $sessionToken The session token.
    * @param integer $expirationTimestamp Timestamp of session expiration.
    * @param string $UID The user's ID.
    * @param string $username The user's username.
    */
    public function __construct(
        string $sessionToken,
        int $expirationTimestamp,
        //string $userID,
        string $email
    ) {
        $this->email             = $email;
        //$this->userID            = $userID;
        $this->sessionToken      = $sessionToken;
        $this->expirationTimestamp  = $expirationTimestamp;
    } //end of contstructor

    //start of getters
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
    // public function getUserId()
    // {
    //     return $this->userID;

    // }//end getUserId()
//end getters

}//end Session class

