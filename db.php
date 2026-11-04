<?php
$host = "sql102.infinityfree.com";
$username = "if0_43347246";
$password = "YOUR_DATABASE_PASSWORD";
$database = "if0_43347246_RakthaMitra";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed");
}

$conn->set_charset("utf8mb4");
?>