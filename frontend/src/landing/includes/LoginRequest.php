<?php
/**
 * Contains class for frontend to request login.
 */
namespace nba\src\landing\includes;
 /**
  * Request from frontend to login.
  */
class LoginRequest{
    
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
    public function __construct(string $email, string $hashedPassword){
        $this->email = $email;
        $this->hashedPassword = $hashedPassword;
    
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