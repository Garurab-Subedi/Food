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

$sql = "DELETE FROM carts WHERE user_id=?";
$stmt = mysqli_prepare($CON, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["success" => true, "message" => "Cart cleared"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to clear cart"]);
}
