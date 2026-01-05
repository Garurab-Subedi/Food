<?php

include("./config/connection.php");
include("./config/auth.php");

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

if (!isset($_POST['cart_id'])) {
    echo json_encode(["success" => false, "message" => "cart_id required"]);
    exit;
}

$cart_id = $_POST['cart_id'];

$sql = "DELETE FROM carts WHERE id=? AND user_id=?";
$stmt = mysqli_prepare($CON, $sql);
mysqli_stmt_bind_param($stmt, "ii", $cart_id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["success" => true, "message" => "Item removed"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to remove"]);
}
