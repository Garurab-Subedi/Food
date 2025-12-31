<?php

include("./config/connection.php");
include("./config/auth.php");

if (isset($_POST['token'])) {
    $token = $_POST['token'];
    $user_id = getUserId($token);


    if (!$user_id) {
        echo json_encode(array(
            "success" => false,
            "message" => "Invalid token"
        ));
        die();
    }


    if (isset($_POST['old_password']) && isset($_POST['new_password'])) {

        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];

        $sql = "select * from users where user_id = $user_id";
        $result = mysqli_query($CON, $sql);

        $user = mysqli_fetch_assoc($result);

        $old_password_hash = $user['password'];

        if (password_verify($old_password, $old_password_hash)) {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

            $sql = "update users set password = '$new_password_hash' where user_id = $user_id";
            $result = mysqli_query($CON, $sql);

            if ($result) {
                echo json_encode(array(
                    "success" => true,
                    "message" => "Password changed successfully"
                ));
            } else {
                echo json_encode(array(
                    "success" => false,
                    "message" => "Failed to change password"
                ));
            }
        } else {
            echo json_encode(array(
                "success" => false,
                "message" => "Incorrect password"
            ));
        }
    } else {
        echo json_encode(array(
            "success" => false,
            "message" => "Old password and new password are required"
        ));
        die();
    }
} else {
    echo json_encode(array(
        "success" => false,
        "message" => "Token not found"
    ));
    die();
}
