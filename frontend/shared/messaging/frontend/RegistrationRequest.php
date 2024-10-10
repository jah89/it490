<?php

/**
 * Contains message for frontend request to register new user.
 */
namespace nba\shared\messaging\frontend;

/**
 * A request from frontend to register new user.
 */
class RegisterRequest extends LoginRequest {
    //Everything is the same in request for now.
    public string $type = 'register_request';

       /**
     * Users email.
     * @var string $email The user's email addr.
     */
    private string $email;

    /**
     * Users' password.
     * 
     * @var string $hashedPassword User's password.
     */
    private string $hashedPassword;

    /**
     * Creates new login request.
     * 
     * @param string $email
     * @param string $password
     */

    public function __construct(string $email, string $hashedPassword, string $type = 'register_request'){
        $this->email = $email;
        $this->hashedPassword = $hashedPassword;
        $this->type = $type;
    
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => $this->type,
            'email' => $this->email,
            'password' => $this->hashedPassword,
        ];
    }
    
    /**
     * Function to get user's email.
     *
     * @return string User's email.
     */
    public function getEmail(){
        return $this->email;
    }

    /**
     * Function to get user's hashed password.
     *
     * @return string User's hashed password.
     */
    public function getPassword(){
        return $this->hashedPassword;
    }
 }
