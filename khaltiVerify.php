<?php

include("./config/connection.php");

$payload = json_decode(file_get_contents("php://input"), true);

$token = $payload['token'];
$amount = $payload['amount']; // in paisa
$order_id = $payload['order_id'];

$ch = curl_init("https://khalti.com/api/v2/payment/verify/");
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Key test_secret_key_xxxxx",
        "Content-Type: application/json"
    ],
    CURLOPT_POSTFIELDS => json_encode([
        "token" => $token,
        "amount" => $amount
    ])
]);

$response = curl_exec($ch);
// curl_close($ch);

$result = json_decode($response, true);

if (isset($result['idx'])) {
    mysqli_query($CON, "UPDATE orders SET payment_status='paid' WHERE id=$order_id");
    echo json_encode(["success" => true]);
} else {
    mysqli_query($CON, "UPDATE orders SET payment_status='failed' WHERE id=$order_id");
    echo json_encode(["success" => false]);
}
