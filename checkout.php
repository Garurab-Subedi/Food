<?php

include("./config/connection.php");
include("./config/auth.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit(json_encode(["success" => false, "message" => "Invalid method"]));
}

$token = getToken();
$user_id = getUserId($token);

if (!$user_id) {
    exit(json_encode(["success" => false, "message" => "Invalid token"]));
}

/* Get cart */
$sql = "
SELECT c.food_id, f.price, c.quantity
FROM carts c
JOIN foods f ON f.id = c.food_id
WHERE c.user_id = ?
";
$stmt = mysqli_prepare($CON, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    exit(json_encode(["success" => false, "message" => "Cart is empty"]));
}

$total = 0;
$items = [];

while ($row = mysqli_fetch_assoc($result)) {
    $row['subtotal'] = $row['price'] * $row['quantity'];
    $total += $row['subtotal'];
    $items[] = $row;
}

/* Create order */
$sql = "INSERT INTO orders (user_id, total_amount, payment_method) VALUES (?, ?, 'khalti')";
$stmt = mysqli_prepare($CON, $sql);
mysqli_stmt_bind_param($stmt, "id", $user_id, $total);
mysqli_stmt_execute($stmt);

$order_id = mysqli_insert_id($CON);

/* Insert order items */
foreach ($items as $item) {
    $sql = "INSERT INTO order_items (order_id, food_id, price, quantity)
            VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($CON, $sql);
    mysqli_stmt_bind_param(
        $stmt,
        "iidi",
        $order_id,
        $item['food_id'],
        $item['price'],
        $item['quantity']
    );
    mysqli_stmt_execute($stmt);
}

/* Clear cart */
mysqli_query($CON, "DELETE FROM carts WHERE user_id=$user_id");

echo json_encode([
    "success" => true,
    "order_id" => $order_id,
    "total" => $total
]);
