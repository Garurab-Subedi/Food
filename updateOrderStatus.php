<?php
include("./config/connection.php");
include("./config/auth.php");

$token = getToken();
$user_id = getUserId($token);

if (!$user_id || !isAdmin($user_id)) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

if (!isset($_POST['order_id'], $_POST['status'])) {
    echo json_encode(["success" => false, "message" => "order_id and status required"]);
    exit;
}

$order_id = (int) $_POST['order_id'];
$status = trim($_POST['status']);

// Optional: validate allowed statuses
$allowed = ["Confirmed", "Processing", "Delivered", "Cancelled"];
if (!in_array($status, $allowed)) {
    echo json_encode(["success" => false, "message" => "Invalid status"]);
    exit;
}

// Prepared statement for security
$stmt = mysqli_prepare($CON, "UPDATE orders SET order_status=? WHERE id=?");
mysqli_stmt_bind_param($stmt, "si", $status, $order_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["success" => true, "message" => "Order status updated"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update order"]);
}
