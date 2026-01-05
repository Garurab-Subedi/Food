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
    f.id,
    f.name,
    f.description,
    f.price,
    f.image,
    f.created_at,
    c.category AS category_name
FROM foods f
JOIN categories c ON c.id = f.category_id
ORDER BY f.id DESC
";

$result = mysqli_query($CON, $sql);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode([
    "success" => true,
    "data" => $data
]);
