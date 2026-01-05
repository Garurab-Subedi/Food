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

$limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 6;

$sql = "
SELECT 
    f.id,
    f.name,
    f.description,
    f.price,
    f.image,
    c.category AS category_name
FROM foods f
JOIN categories c ON c.id = f.category_id
ORDER BY RAND()
LIMIT ?
";

$stmt = mysqli_prepare($CON, $sql);
mysqli_stmt_bind_param($stmt, "i", $limit);
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
