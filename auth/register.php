<?php
header("Content-Type: application/json");
include("../config/connection.php");

if (isset($_POST['email'], $_POST['password'], $_POST['fullName'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];
    $fullName = $_POST['fullName'];

    global $CON;

    $sql = "select * from users where email = '$email'";

    $result = mysqli_query($CON, $sql);

    $count = mysqli_num_rows($result);

    if ($count > 0) {
        echo json_encode(array(
            "success" => false,
            "message" => "Email already exists"
        ));
        die();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "insert into users (email,password,full_name,role) values ('$email','$hashedPassword','$fullName','user')";

    $result = mysqli_query($CON, $sql);


    if (!$result) {
        echo json_encode(array(
            "success" => false,
            "message" => "User registration failed"
        ));
        die();
    }


    echo json_encode(array(
        "success" => true,
        "message" => "User registered successfully"
    ));
} else {
    echo json_encode(array(
        "success" => false,
        "message" => "email, password and fullName is required"
    ));
}
