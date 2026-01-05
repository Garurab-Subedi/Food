<?php

include("./config/connection.php");
include("./config/auth.php");

if (!isset($_POST['token'], $_POST['category_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "token and category_id are required"
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

$category_id = $_POST['category_id'];

/* OPTIONAL: CHECK IF CATEGORY EXISTS */
$sql = "SELECT id FROM categories WHERE id = ? LIMIT 1";
$stmt = mysqli_prepare($CON, $sql);
mysqli_stmt_bind_param($stmt, "i", $category_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    echo json_encode([
        "success" => false,
        "message" => "Category not found"
    ]);
    exit;
}

/* DELETE CATEGORY */
$sql = "DELETE FROM categories WHERE id = ?";
$stmt = mysqli_prepare($CON, $sql);
mysqli_stmt_bind_param($stmt, "i", $category_id);
$delete = mysqli_stmt_execute($stmt);

if ($delete) {
    echo json_encode([
        "success" => true,
        "message" => "Category deleted successfully"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Failed to delete category",
        "error" => mysqli_error($CON)
    ]);
}
