<?php

function connectDB(){

    $host = '172.30.17.239';  
    $user = 'eait490';           
    $password = 'teamfantasy'; 
    $dbname = 'nba';      

    $mydb = new mysqli($host, $user, $password, $dbname);

    if ($mydb->connect_error) {
        echo "Failed to connect to database: " . $mydb->connect_error . PHP_EOL;
        exit(0);
    }

    echo "Successfully connected to database." . PHP_EOL;
}
?>