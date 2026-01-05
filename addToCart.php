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
if (!$user_id) {
    echo json_encode(["success" => false, "message" => "Invalid token"]);
    exit;
}

/* ===== INPUT ===== */
if (!isset($_POST['food_id'])) {
    echo json_encode(["success" => false, "message" => "food_id required"]);
    exit;
}

$food_id = $_POST['food_id'];
$qty = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

/* ===== INSERT OR UPDATE ===== */
$sql = "
INSERT INTO carts (user_id, food_id, quantity)
VALUES (?, ?, ?)
ON DUPLICATE KEY UPDATE quantity = quantity + ?
";

$stmt = mysqli_prepare($CON, $sql);
mysqli_stmt_bind_param($stmt, "iiii", $user_id, $food_id, $qty, $qty);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["success" => true, "message" => "Added to cart"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to add cart"]);
}
