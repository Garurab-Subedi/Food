<?php
header("Content-Type: application/json");
include("../config/connection.php");

// Check POST
if (isset($_POST['email'], $_POST['password'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    global $CON;

    // Check if email exists
    $sql = "SELECT * FROM users WHERE email='$email' AND role='admin'";
    $result = mysqli_query($CON, $sql);

    if (mysqli_num_rows($result) == 0) {
        echo json_encode([
            "success" => false,
            "message" => "Admin not found!"
        ]);
        exit;
    }

    $admin = mysqli_fetch_assoc($result);
    $hashedPassword = $admin['password'];

    // Verify password
    if (!password_verify($password, $hashedPassword)) {
        echo json_encode([
            "success" => false,
            "message" => "Incorrect password"
        ]);
        exit;
    }

    // Generate token
    $token = bin2hex(random_bytes(32));
    $admin_id = $admin['user_id'];

    // Insert token
    $sql = "INSERT INTO access_tokens (token, user_id) VALUES ('$token', '$admin_id')";
    $result = mysqli_query($CON, $sql);

    if (!$result) {
        echo json_encode([
            "success" => false,
            "message" => "Failed to log in"
        ]);
    } else {
        echo json_encode([
            "success" => true,
            "message" => "Admin logged in successfully!",
            "token" => $token,
            "role" => $admin['role'],
            "admin_name" => $admin['full_name']
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Email and password are required"
    ]);
}
