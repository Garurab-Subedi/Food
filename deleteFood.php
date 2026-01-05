<?php

include("./config/connection.php");
include("./config/auth.php");

$token = getToken();

if (!$token) {
    echo json_encode(["success" => false, "message" => "Token not found"]);
    exit;
}

$user_id = getUserId($token);

if (!$user_id || !isAdmin($user_id)) {
    echo json_encode(["success" => false, "message" => "You are not authorized"]);
    exit;
}

if (!isset($_POST['food_id'])) {
    echo json_encode(["success" => false, "message" => "food_id required"]);
    exit;
}

$food_id = $_POST['food_id'];

$sql = "DELETE FROM foods WHERE id = ?";
$stmt = mysqli_prepare($CON, $sql);
mysqli_stmt_bind_param($stmt, "i", $food_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["success" => true, "message" => "Food deleted successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Delete failed"]);
}
