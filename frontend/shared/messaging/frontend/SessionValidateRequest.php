<?php
/**
 * Contains request for frontend to 
 * validate session token with db.
 */
namespace nba\shared\messaging\frontend;

/**
 * A request from frontend to validate a session token.
 */
class SessionValidateRequest  extends SessionRequest
{
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
    private string $type;


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
    public function __construct(string $type= 'validate_request', string $sessionToken)
    {
        $this->type = $type;
        $this->sessionToken = $sessionToken;

    }

    public function jsonSerialize(): mixed
        {
            return [
                'type' => $this->type,
                'token' => $this->sessionToken,

            ];
        }

    /**
     * Gets result of request.
     *
     * @return boolean True for successful validation or invalidation, false if not valid or there is an error.
     */
    public function getToken()
    {
        return $this->sessionToken;

    }


}