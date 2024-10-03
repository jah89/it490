<?php
/**
 * Initial index page redirects user to landing page.
 */

 namespace NBA\Frontend\Src;
require (__DIR__.'/../vendor/autoload.php');
header('Location: /landing');
exit();