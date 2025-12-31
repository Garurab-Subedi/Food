<?php
include("../config/connection.php");

if (isset($_POST['email'], $_POST['password'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    global $CON;

    // PREPARED STATEMENT FOR SECURITY
    $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
    $stmt = mysqli_prepare($CON, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result || mysqli_num_rows($result) == 0) {
        echo json_encode([
            "success" => false,
            "message" => "User not found!"
        ]);
        exit;
    }

    $user = mysqli_fetch_assoc($result);

    if (!password_verify($password, $user['password'])) {
        echo json_encode([
            "success" => false,
            "message" => "Incorrect password"
        ]);
        exit;
    }

    // TOKEN GENERATION
    $token = bin2hex(random_bytes(32));

    $sql = "INSERT INTO access_tokens (token, user_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($CON, $sql);
    mysqli_stmt_bind_param($stmt, "si", $token, $user['id']);
    $insert = mysqli_stmt_execute($stmt);

    if (!$insert) {
        echo json_encode([
            "success" => false,
            "message" => "Failed to log in"
        ]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "message" => "Logged in successfully!",
        "token" => $token,
        "role" => $user['role']
    ]);
    exit;
}

echo json_encode([
    "success" => false,
    "message" => "Email and password is required"
]);
