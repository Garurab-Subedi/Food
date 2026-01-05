<?php

include("./config/connection.php");
include("./config/auth.php");

/* AUTH */
$token = getToken();
$user_id = getUserId($token);
if (!$user_id) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

/* INPUT */
if (!isset($_POST['payment_method'])) {
    echo json_encode(["success" => false, "message" => "payment_method required"]);
    exit;
}

$payment_method = strtoupper($_POST['payment_method']);
$allowed = ['COD', 'IPS', 'KHALTI'];

if (!in_array($payment_method, $allowed)) {
    echo json_encode(["success" => false, "message" => "Invalid payment method"]);
    exit;
}

/* CALCULATE CART TOTAL */
$sql = "SELECT SUM(f.price * c.quantity) total
        FROM carts c
        JOIN foods f ON f.id=c.food_id
        WHERE c.user_id=?";
$stmt = mysqli_prepare($CON, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$total = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

if (!$total) {
    echo json_encode(["success" => false, "message" => "Cart empty"]);
    exit;
}

/* CREATE ORDER */
$sql = "INSERT INTO orders (user_id,total_amount,payment_method)
        VALUES (?,?,?)";
$stmt = mysqli_prepare($CON, $sql);
mysqli_stmt_bind_param($stmt, "ids", $user_id, $total, $payment_method);
mysqli_stmt_execute($stmt);
$order_id = mysqli_insert_id($CON);

/* PAYMENT LOGIC */
if ($payment_method === 'COD') {

    mysqli_query($CON, "INSERT INTO payments 
        (order_id,payment_method,amount,status)
        VALUES ($order_id,'COD',$total,'pending')");

    echo json_encode([
        "success" => true,
        "message" => "Order placed with COD",
        "order_id" => $order_id
    ]);
}

/* IPS (manual payment) */ elseif ($payment_method === 'IPS') {

    mysqli_query($CON, "INSERT INTO payments 
        (order_id,payment_method,amount,status)
        VALUES ($order_id,'IPS',$total,'pending')");

    echo json_encode([
        "success" => true,
        "message" => "Order placed. Complete IPS payment.",
        "order_id" => $order_id,
        "bank_details" => "Nabil Bank | AC: 123456"
    ]);
}

/* KHALTI */ else {

    mysqli_query($CON, "INSERT INTO payments 
        (order_id,payment_method,amount,status)
        VALUES ($order_id,'KHALTI',$total,'pending')");

    echo json_encode([
        "success" => true,
        "order_id" => $order_id,
        "khalti_amount" => $total * 100
    ]);
}

/* CLEAR CART */
mysqli_query($CON, "DELETE FROM carts WHERE user_id=$user_id");
