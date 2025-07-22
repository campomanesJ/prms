<?php
// config/db.php

$host = 'localhost';         // Change if your host is different
$user = 'root';              // Change according to your DB username
$password = '';              // Change according to your DB password
$database = 'prms';     // Replace with your database name

$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
