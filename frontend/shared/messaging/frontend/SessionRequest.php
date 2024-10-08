<?php
/**
 * Contains message of session requests for frontend.
 *
 */

 namespace nba\shared\messaging\frontend;

use JsonSerializable;

/**
 * A request from the frontend for a session operation. Other classes are based on this.
 */
abstract class SessionRequest implements JsonSerializable {

    /**
     * Users email.
     * @var string $email The user's email addr.
     */
    private string $email;

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
    public function __construct(string $type='session_request', string $email)
    {
        $this->type         = $type;
        $this->email        = $email;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => $this->type,
            'payload' => [
                'email' => $this->email,
            ]
        ];
    }
    }