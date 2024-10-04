<?php
/**
 * Contains response from DB to frontend to end
 * a session by invalidating session token.
 */

 namespace nba\shared\messaging\frontend;


/**
 * A request from the frontend to invalidate
 * a user's session via token invalidation.
 */
class SessionTerminateResponse extends SessionResponse
{
    //No additional logic needed right now.
}