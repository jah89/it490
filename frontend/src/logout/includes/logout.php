<?php
namespace nba\fronend\src\logout;

session_start();
reset_session();

header("Location: /landing");
?>