<?php
/**
 * Contains response from DB processor for a login request.
 */
namespace NBA\Frontend\Messaging;


/**
 * Response from DB processor to login request.
 */
abstract class LoginResponse
{

    /**
     * True for success, false for fail.
     *
     * @var boolean $result
     */
    private bool $result;

    /**
     * Session object.
     *
     * @var NBA\Shared\Session $session User session object.
     */
    private Session $session;


    /**
     * Creates a new login response.
     *
     * @param boolean              $result  True if succesfully logged in, otherwise false.
     * @param \JAND\Common\Session $session The session object, defaults to null.
     */
    public function __construct(bool $result, Session $session=null)
    {
        $this->result  = $result;
        $this->session = $session;

    }


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
     * Gets session.
     *
     * @return NBA\Shared\Session Session object.
     * 
     */
    public function getSession()
    {
        return $this->session;

    }


}//end class