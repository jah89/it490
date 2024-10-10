<?php
/**
 * Contains class for frontend to request login.
 */
namespace nba\shared\messaging\frontend;

 /**
  * Request from frontend to login.
  */
class LoginRequest extends \nba\shared\messaging\Request{
    
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
    public function __construct(string $email, string $hashedPassword, string $type = 'login_request'){
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