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
}