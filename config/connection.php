<?php

$host = 'localhost';
$user = 'root';
$password = '';
$db = 'fooddelivery';

$CON = mysqli_connect($host, $user, $password, $db);

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// For OPTIONS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}


if (!$CON) {
    echo "Connection Failed";
}
