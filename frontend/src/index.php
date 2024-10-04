<?php
/**
 * Initial index page redirects user to landing page.
 */
namespace nba\src;
require (__DIR__.'/../vendor/autoload.php');

header('Location: /landing');

// exit();