<?php

include("./config/connection.php");
include("./config/auth.php");

$token = getToken();
$user_id = getUserId($token);

if (!$user_id) {
    exit(json_encode(["success" => false, "message" => "Invalid token"]));
}

$sql = "
SELECT id, total_amount, payment_status, order_status, created_at
FROM orders
WHERE user_id = ?
ORDER BY id DESC
";

$stmt = mysqli_prepare($CON, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode(["success" => true, "data" => $data]);
