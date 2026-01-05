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

$sql = "
SELECT 
    c.id AS cart_id,
    f.id AS food_id,
    f.name,
    f.price,
    f.image,
    c.quantity,
    (f.price * c.quantity) AS total_price
FROM carts c
JOIN foods f ON f.id = c.food_id
WHERE c.user_id = ?
ORDER BY c.id DESC
";

$stmt = mysqli_prepare($CON, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$items = [];
$grand_total = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;
    $grand_total += $row['total_price'];
}

echo json_encode([
    "success" => true,
    "items" => $items,
    "grand_total" => $grand_total
]);
