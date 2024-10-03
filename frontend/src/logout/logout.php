<?php
namespace NBA\Frontend\Pages;

session_start();
reset_session();

header("Location: /landing");
?>