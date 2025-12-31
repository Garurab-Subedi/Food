<?php

$host = 'localhost';
$user = 'root';
$password = '';
$db = 'fooddelivery';

$CON = mysqli_connect($host, $user, $password, $db);


if (!$CON) {
    echo "Connection Failed";
}
