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

if (!isset($_POST['cart_id'], $_POST['quantity'])) {
    echo json_encode(["success" => false, "message" => "cart_id and quantity required"]);
    exit;
}

$cart_id = $_POST['cart_id'];
$qty = (int)$_POST['quantity'];

if ($qty <= 0) {
    echo json_encode(["success" => false, "message" => "Quantity must be >= 1"]);
    exit;
}

$sql = "UPDATE carts SET quantity=? WHERE id=? AND user_id=?";
$stmt = mysqli_prepare($CON, $sql);
mysqli_stmt_bind_param($stmt, "iii", $qty, $cart_id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["success" => true, "message" => "Cart updated"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update"]);
}
