<?php

include("./config/connection.php");
include("./config/auth.php");

$token = getToken();
$user_id = getUserId($token);

if (!$user_id || !isAdmin($user_id)) {
    exit(json_encode(["success" => false, "message" => "Unauthorized"]));
}

$sql = "
SELECT o.id, u.full_name, o.total_amount, o.payment_status, o.order_status, o.created_at
FROM orders o
JOIN users u ON u.id = o.user_id
ORDER BY o.id DESC
";

$result = mysqli_query($CON, $sql);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode(["success" => true, "data" => $data]);
