<?php

include("./config/connection.php");
include("./config/auth.php");

if (!isset($_POST['token'], $_POST['category_id'], $_POST['category_name'])) {
    echo json_encode([
        "success" => false,
        "message" => "token, category_id and category_name are required"
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

$category_id   = $_POST['category_id'];
$category_name = trim($_POST['category_name']);

/* CHECK DUPLICATE NAME */
$sql = "SELECT id FROM categories WHERE category = ? AND id != ? LIMIT 1";
$stmt = mysqli_prepare($CON, $sql);
mysqli_stmt_bind_param($stmt, "si", $category_name, $category_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    echo json_encode([
        "success" => false,
        "message" => "Category name already exists"
    ]);
    exit;
}

/* UPDATE CATEGORY */
$sql = "UPDATE categories SET category = ? WHERE id = ?";
$stmt = mysqli_prepare($CON, $sql);
mysqli_stmt_bind_param($stmt, "si", $category_name, $category_id);
$update = mysqli_stmt_execute($stmt);

if ($update) {
    echo json_encode([
        "success" => true,
        "message" => "Category updated successfully"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Failed to update category",
        "error" => mysqli_error($CON)
    ]);
}
