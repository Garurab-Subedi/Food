<?php

include("./config/connection.php");
include("./config/auth.php");

/* ================= TOKEN CHECK ================= */

if (!isset($_POST['token'])) {
    echo json_encode(["success" => false, "message" => "Token not found"]);
    exit;
}

$token = $_POST['token'];
$user_id = getUserId($token);

if (!$user_id) {
    echo json_encode(["success" => false, "message" => "Invalid token"]);
    exit;
}

if (!isAdmin($user_id)) {
    echo json_encode(["success" => false, "message" => "You are not authorized"]);
    exit;
}

/* ================= INPUT CHECK ================= */

if (
    !isset(
        $_POST['food_name'],
        $_POST['description'],
        $_POST['price'],
        $_POST['category_id'],
        $_FILES['image']
    )
) {
    echo json_encode([
        "success" => false,
        "message" => "food_name, description, price, category_id and image are required"
    ]);
    exit;
}

$food_name   = trim($_POST['food_name']);
$description = trim($_POST['description']);
$price       = $_POST['price'];
$category_id = $_POST['category_id'];
$image       = $_FILES['image'];

/* ================= IMAGE VALIDATION ================= */

if ($image['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["success" => false, "message" => "Image upload error"]);
    exit;
}

$allowed = ["jpg", "jpeg", "png", "webp"];
$ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    echo json_encode(["success" => false, "message" => "Only JPG, JPEG, PNG, WEBP allowed"]);
    exit;
}

if ($image['size'] > 5 * 1024 * 1024) {
    echo json_encode(["success" => false, "message" => "Image must be less than 5MB"]);
    exit;
}

/* ================= IMAGE UPLOAD ================= */


$image_dir = "/Applications/XAMPP/xamppfiles/htdocs/fooddeliverY/image/";

if (!is_dir($image_dir)) {
    mkdir($image_dir, 0777, true);
}

$new_image_name = time() . "-" . rand(10000, 999999) . "." . $ext;
$upload_path = $image_dir . $new_image_name;
$image_url = "/image/" . $new_image_name;

if (!move_uploaded_file($image['tmp_name'], $upload_path)) {
    echo json_encode([
        "success" => false,
        "message" => "Failed to upload image"
    ]);
    exit;
}

/* ================= INSERT DATA ================= */

$sql = "INSERT INTO foods 
        (name, description, price, category_id, image, user_id)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($CON, $sql);
mysqli_stmt_bind_param(
    $stmt,
    "ssdisi",
    $food_name,
    $description,
    $price,
    $category_id,
    $image_url,
    $user_id
);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        "success" => true,
        "message" => "Food added successfully"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Failed to add food",
        "error" => mysqli_error($CON)
    ]);
}
