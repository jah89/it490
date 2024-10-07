<?php
/**
 * Contains response from DB processor to frontend about sessions.
 */

 namespace nba\shared\messaging\frontend;

use JsonSerializable;

/**
 * Contains response from DB processor to frontend about sessions.
 */
abstract class SessionResponse implements JsonSerializable
{

        /**
     * Users email.
     * @var string $email The user's email addr.
     */
    private string $email;

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
    private string $type = 'session_response';

    /**
     * True for successful validation, false if not valid or there is an error.
     *
     * @var boolean $result
     */
    private bool $result;

        /**
     * Session expiration time using Unix epoch timestamp.
     *
     * @var string $expirationTimestamp is the timestamp of session expiration.
     */
    private string $expirationTimestamp;


    /**
     * Create new session response.
     *
     * @param boolean $result True for validation, false if not valid or there is an error.
     */
    public function __construct(string $type, bool $result, string $email, string $sessionToken, int $expirationTimestamp)
    {
        $this->type = $type;
        $this->result = $result;
        $this->email = $email;
        $this->sessionToken = $sessionToken;
        $this->expirationTimestamp = $expirationTimestamp;

    }

    public function jsonSerialize(): mixed
        {
            return [
                'type' => $this->type,
                'payload' => [
                    'result' =>$this->result,
                    'email' => $this->email,
                    'token' => $this->sessionToken,
                    'timestamp' => $this->expirationTimestamp
                ]
            ];
        }

    /**
     * Gets result of session request.
     *
     * @return boolean True for successful validation or invalidation, false if not valid or there is an error.
     */
    public function getResult()
    {
        return $this->result;

    }

        /**
     * Gets result of session request.
     *
     * @return boolean True for successful validation or invalidation, false if not valid or there is an error.
     */
    public function getToken()
    {
        return $this->sessionToken;

    }
        /**
     * Gets timestamp from session response.
     *
     * @return boolean True for successful validation or invalidation, false if not valid or there is an error.
     */
    public function getTimestamp()
    {
        return $this->expirationTimestamp;

    }


}