<?php
/**
 * Initial index page redirects user to landing page.
 */
namespace NBA\Frontend\src;
require (__DIR__.'/../vendor/autoload.php');

header('Location: /landing');

// exit();