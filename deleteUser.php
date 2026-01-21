<?php
include("./config/connection.php");
include("./config/auth.php");

$token = getToken();
if (!$token) {
    echo json_encode(["success" => false, "message" => "Token missing"]);
    exit;
}

$user_id = getUserId($token);
if (!$user_id || !isAdmin($user_id)) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

if (!isset($_POST['user_id'])) {
    echo json_encode(["success" => false, "message" => "user_id required"]);
    exit;
}

$delete_id = (int) $_POST['user_id'];

if ($delete_id === (int)$user_id) {
    echo json_encode(["success" => false, "message" => "You cannot delete yourself"]);
    exit;
}

$stmt = mysqli_prepare($CON, "DELETE FROM users WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $delete_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["success" => true, "message" => "User deleted successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Delete failed"]);
}
