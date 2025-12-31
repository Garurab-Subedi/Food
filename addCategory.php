<?php

include("./config/connection.php");
include("./config/auth.php");

if (!isset($_POST['token'])) {
    echo json_encode([
        "success" => false,
        "message" => "Token not found"
    ]);
    exit;
}

$token = $_POST['token'];
$user_id = getUserId($token);

if (!$user_id) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid token"
    ]);
    exit;
}

if (!isAdmin($user_id)) {
    echo json_encode([
        "success" => false,
        "message" => "You are not authorized"
    ]);
    exit;
}

if (!isset($_POST['category_name'])) {
    echo json_encode([
        "success" => false,
        "message" => "Category name is required"
    ]);
    exit;
}

$category_name = trim($_POST['category_name']);

// CHECK IF CATEGORY EXISTS (prepared statement)
$sql = "SELECT id FROM categories WHERE category = ? LIMIT 1";
$stmt = mysqli_prepare($CON, $sql);
mysqli_stmt_bind_param($stmt, "s", $category_name);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    echo json_encode([
        "success" => false,
        "message" => "Category already exists"
    ]);
    exit;
}

// INSERT CATEGORY (prepared statement)
$sql = "INSERT INTO categories (category) VALUES (?)";
$stmt = mysqli_prepare($CON, $sql);
mysqli_stmt_bind_param($stmt, "s", $category_name);
$insert = mysqli_stmt_execute($stmt);

if ($insert) {
    echo json_encode([
        "success" => true,
        "message" => "Category added successfully"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Failed to add category",
        "error" => mysqli_error($CON)  // DEBUGGING
    ]);
}
