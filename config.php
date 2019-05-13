<?php

/**
  * Configuration for database connection
  *
  */

$host       = "127.0.0.1";
$username   = "root";
$password   = "y0ungerareyou";
$dbname     = "ehrdb"; // will use later
$dsn        = "mysql:host=$host;dbname=$dbname"; // will use later
$options    = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
              );
/* Attempt to connect to MySQL database */
$link = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

?>