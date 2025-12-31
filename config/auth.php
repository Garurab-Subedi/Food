<?php
include("./config/connection.php");

/**
 * Get token from POST body or Authorization header
 */
function getToken()
{
    // 1️⃣ POST body
    if (!empty($_POST['token'])) {
        return $_POST['token'];
    }

    // 2️⃣ Authorization header (Apache-safe)
    if (function_exists('getallheaders')) {
        $headers = getallheaders();

        if (!empty($headers['Authorization'])) {
            // Bearer token support
            if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
                return $matches[1];
            }
            return trim($headers['Authorization']);
        }
    }

    return null;
}

function getUserId($token)
{
    global $CON;

    $sql = "SELECT user_id FROM access_tokens WHERE token = ? LIMIT 1";
    $stmt = mysqli_prepare($CON, $sql);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result || mysqli_num_rows($result) === 0) {
        return null;
    }

    return mysqli_fetch_assoc($result)['user_id'];
}

function isAdmin($userId)
{
    global $CON;

    $sql = "SELECT role FROM users WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($CON, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result || mysqli_num_rows($result) === 0) {
        return false;
    }

    return mysqli_fetch_assoc($result)['role'] === 'admin';
}
