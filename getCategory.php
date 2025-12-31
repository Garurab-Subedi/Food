<?php

include("./config/connection.php");
include("./config/auth.php");

$token = getToken();

if (!$token) {
    echo json_encode([
        "success" => false,
        "message" => "Token not found"
    ]);
    exit;
}

$user_id = getUserId($token);

if (!$user_id) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid token"
    ]);
    exit;
}

$sql = "SELECT id, category, created_at FROM categories ORDER BY id DESC";
$result = mysqli_query($CON, $sql);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode([
    "success" => true,
    "data" => $data
]);
