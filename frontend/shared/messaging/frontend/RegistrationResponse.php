<?php

/**
 * Contains message for DB response to registering a new user.
 */
namespace nba\shared\messaging\frontend;

/**
 * The DB response to a request from frontend to register new user.
 */
class RegisterResponse extends LoginResponse{
    public string $type = 'register_response';
}