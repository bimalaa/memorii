<?php

$servername = "localhost";
$usernames = "root"; 
$password = ""; 
$dbname = "memori"; 

$conn = new mysqli($servername, $usernames, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>