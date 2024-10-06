<?php
/**
 * Contains response from DB processor for a login request.
 */
namespace nba\shared\messaging\frontend;

/**
 * Response from DB connector for a login request.
 */

class ConcreteLoginResponse extends LoginResponse
{
    public function __construct(bool $result, \nba\shared\Session $session = null)
    {
        parent::__construct($result, $session);
    }
}
?>