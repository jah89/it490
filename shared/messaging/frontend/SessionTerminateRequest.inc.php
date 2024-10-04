<?php
/**
 * Contains request for frontend to end
 * a session by invalidating session token.
 */

 namespace NBA\Frontend\Messaging;


/**
 * A request from the frontend to invalidate
 * a user's session via token invalidation.
 */
class SessionTerminateRequest extends SessionRequest
{
}