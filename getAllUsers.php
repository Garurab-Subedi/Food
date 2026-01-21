<?php

include("./config/connection.php");
include("./config/auth.php");

/* ===== AUTH ===== */
$token = getToken();

if (!$token) {
    echo json_encode(["success" => false, "message" => "Token not found"]);
    exit;
}

$user_id = getUserId($token);

if (!$user_id || !isAdmin($user_id)) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

/* ===== FETCH USERS ===== */
$sql = "SELECT id, full_name, email, role, created_at FROM users ORDER BY id DESC";
$result = mysqli_query($CON, $sql);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode([
    "success" => true,
    "data" => $data
]);
