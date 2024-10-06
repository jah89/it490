<?php

/**
 * Contains message for DB response to registering a new user.
 */
namespace nba\shared\messaging\frontend;

/**
 * The DB response to a request from frontend to register new user.
 */
class RegisterResponse extends LoginResponse{
    
    private \nba\shared\messaging\MessageError $error;

    public function __construct(bool $result,
    \nba\shared\Session $session,
    \nba\shared\messaging\MessageError $error = null)
    {
        //Constructor of parent LoginResponse class
        parent::__construct($result, $session);
        $this->error = $error;
        
    }

    public function getMessageError(){
        return $this->error;
    }
}