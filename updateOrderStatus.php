<?php
include("./config/connection.php");
include("./config/auth.php");

$token = getToken();
$user_id = getUserId($token);

if (!$user_id || !isAdmin($user_id))
    exit(json_encode(["success" => false]));

if (!isset($_POST['order_id'], $_POST['status']))
    exit(json_encode(["success" => false]));

$order_id = $_POST['order_id'];
$status = $_POST['status'];

mysqli_query(
    $CON,
    "UPDATE orders SET order_status='$status' WHERE id=$order_id"
);

echo json_encode(["success" => true]);
