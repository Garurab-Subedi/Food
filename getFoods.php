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

if (!isset($_POST['category_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "category_id is required"
    ]);
    exit;
}

$category_id = $_POST['category_id'];

$sql = "
SELECT 
    f.id,
    f.name,
    f.description,
    f.price,
    f.image,
    f.created_at,
    c.category AS category_name
FROM foods f
JOIN categories c ON c.id = f.category_id
WHERE f.category_id = ?
ORDER BY f.id DESC
";

$stmt = mysqli_prepare($CON, $sql);
mysqli_stmt_bind_param($stmt, "i", $category_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$foods = [];

while ($row = mysqli_fetch_assoc($result)) {
    $foods[] = $row;
}

echo json_encode([
    "success" => true,
    "data" => $foods
]);
