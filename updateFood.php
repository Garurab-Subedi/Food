<?php

include("./config/connection.php");
include("./config/auth.php");

/* ===== AUTH ===== */
$token = getToken();

if (!$token) {
    echo json_encode(["success" => false, "message" => "Token not found"]);
    exit;
}

$user_id = getUserId($token);

if (!$user_id || !isAdmin($user_id)) {
    echo json_encode(["success" => false, "message" => "You are not authorized"]);
    exit;
}

/* ===== INPUT ===== */
if (!isset($_POST['food_id'], $_POST['food_name'], $_POST['description'], $_POST['price'], $_POST['category_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "food_id, food_name, description, price, category_id required"
    ]);
    exit;
}

$food_id     = $_POST['food_id'];
$food_name   = trim($_POST['food_name']);
$description = trim($_POST['description']);
$price       = $_POST['price'];
$category_id = $_POST['category_id'];

/* ===== IMAGE (OPTIONAL) ===== */
$image_sql = "";
$params = [$food_name, $description, $price, $category_id];
$types = "ssdi";

if (!empty($_FILES['image']['name'])) {

    $allowed = ["jpg", "jpeg", "png", "webp"];
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        echo json_encode(["success" => false, "message" => "Invalid image type"]);
        exit;
    }

    $image_dir = "/Applications/XAMPP/xamppfiles/htdocs/fooddelivery/image/";
    $new_name = time() . "-" . rand(10000, 999999) . "." . $ext;
    $upload_path = $image_dir . $new_name;
    $image_url = "/image/" . $new_name;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
        echo json_encode(["success" => false, "message" => "Image upload failed"]);
        exit;
    }

    $image_sql = ", image = ?";
    $params[] = $image_url;
    $types .= "s";
}

/* ===== UPDATE ===== */
$sql = "UPDATE foods SET name=?, description=?, price=?, category_id=? $image_sql WHERE id=?";

$params[] = $food_id;
$types .= "i";

$stmt = mysqli_prepare($CON, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$params);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["success" => true, "message" => "Food updated successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Update failed"]);
}
