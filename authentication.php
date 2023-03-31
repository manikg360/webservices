<?php

require_once 'config.php';

// Authentication APIs
header('Content-Type: application/json; charset=utf-8');
$data = [];
session_start();
$postData = json_decode(file_get_contents('php://input'), true);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($postData['username']) && isset($postData['password'])) {
    $username = mysqli_real_escape_string($conn, $postData['username']);
    $password = mysqli_real_escape_string($conn, $postData['password']);
    $sql = "SELECT * FROM users WHERE name='$username'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $hashed_password = $user['password'];
        if (password_verify($password, $hashed_password)) {
            // Password is correct
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['is_admin'] = $user['is_admin'];
            http_response_code(200);
            $data['status'] = 'success';
            $data['message'] = 'User authenticated successfully.';
            echo json_encode($data);exit;
        } else {
            // Password is incorrect
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['is_admin'] = $user['is_admin'];
            http_response_code(401);
            $data['status'] = 'fail';
            $data['message'] = 'Invalid username or password.';
            echo json_encode($data);exit;
        }
        
    } else {
        http_response_code(401);
        $data['status'] = 'fail';
        $data['message'] = 'Invalid username or password.';
        echo json_encode($data);exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_GET['logout'])) {
        session_destroy();
        http_response_code(200);
        $data['status'] = 'success';
        $data['message'] = 'User logout successfully.';
        echo json_encode($data);
    } else {
        http_response_code(401);
        $data['status'] = 'fail';
        $data['message'] = 'Invalid/Missing parameters.';
        echo json_encode($data);
    }
}

?>
