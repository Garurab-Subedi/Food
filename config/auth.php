<?php
include("./config/connection.php");

function getUserId($token)
{
    global $CON;

    $sql = "SELECT user_id FROM access_tokens WHERE token = ? LIMIT 1";
    $stmt = mysqli_prepare($CON, $sql);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result || mysqli_num_rows($result) == 0) {
        return null;
    }

    $row = mysqli_fetch_assoc($result);
    return $row['user_id'];
}

function isAdmin($userId)
{
    global $CON;

    $sql = "SELECT role FROM users WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($CON, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result || mysqli_num_rows($result) == 0) {
        return false;
    }

    $row = mysqli_fetch_assoc($result);

    return $row['role'] === 'admin';
}
