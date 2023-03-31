<?php

require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');
$postData = json_decode(file_get_contents('php://input'), true);
$data = [];
// Manage Users
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($postData['name']) && isset($postData['email']) && isset($postData['password'])) {
        $name = mysqli_real_escape_string($conn, $postData['name']);
        $email = mysqli_real_escape_string($conn, $postData['email']);
        $password = mysqli_real_escape_string($conn, $postData['password']);
        // Generate a random salt
        $salt = password_hash($password, PASSWORD_DEFAULT);
        // Hash the password with the salt
        $password = password_hash($password, PASSWORD_BCRYPT, ['salt' => $salt]);
        $is_admin = isset($postData['is_admin']) ? 1 : 0;
        $sql = "INSERT INTO users (name, email, password, is_admin) VALUES ('$name', '$email', '$password', '$is_admin')";
        $record = mysqli_query($conn, $sql);
        if ($record) {
            $last_inserted_id = mysqli_insert_id($conn);
            // Query the database to retrieve the last inserted record
            $query = "SELECT * FROM users WHERE id = $last_inserted_id";
            $result = mysqli_query($conn, $query);
            
            // Fetch the record as an associative array
            $record = mysqli_fetch_assoc($result);
            http_response_code(201);
            $data['data'] = $record;
            $data['status'] = 'success';
            $data['message'] = 'User created successfully.';
            echo json_encode($data);
        } else {
            $error = mysqli_error($conn);
            http_response_code(500);
            $data['status'] = 'fail';
            if($error) {
                $data['message'] = $error;
            } else {
                $data['message'] = 'Some error occured please try again after sometime.';
            }
            echo json_encode($data);
        }
    } else {
        http_response_code(500);
        $data['status'] = 'fail';
        $data['message'] = 'Invalid parameters or parameters missing.';
        echo json_encode($data);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    if(isset($_GET['id']) && isset($postData['name'])) {
        $id = mysqli_real_escape_string($conn, $_GET['id']);
        $is_admin = isset($postData['is_admin']) ? 1 : 0;
        $update = "SET is_admin='$is_admin'";
        if(isset($postData['name']) && !empty($postData['name'])) {
            $name = mysqli_real_escape_string($conn, $postData['name']);
            $update .= ",name='$name'";
        }
        if(isset($postData['email']) && !empty($postData['email'])) {
            $email = mysqli_real_escape_string($conn, $postData['email']);
            $update .= ",email='$email'";
        }
        if(isset($postData['password']) && !empty($postData['password'])) {
            $password = mysqli_real_escape_string($conn, $postData['password']);
            // Generate a random salt
            $salt = password_hash($password, PASSWORD_DEFAULT);
            // Hash the password with the salt
            $password = password_hash($password, PASSWORD_BCRYPT, ['salt' => $salt]);
            $update .= ",password='$password'";
        }
        $sql = "UPDATE users $update WHERE id='$id'";
        if (mysqli_query($conn, $sql)) {
            // Query the database to retrieve the last inserted record
            $query = "SELECT * FROM users WHERE id = $id";
            $result = mysqli_query($conn, $query);
            
            // Fetch the record as an associative array
            $record = mysqli_fetch_assoc($result);
            http_response_code(200);
            $data['data'] = $record;
            $data['status'] = 'fail';
            $data['message'] = 'User has been updated successfully.';
            echo json_encode($data);
        } else {
            http_response_code(500);
            $data['status'] = 'fail';
            $data['message'] = 'Some error occured please try again after sometime.';
            echo json_encode($data);
        }
    } else {
        http_response_code(500);
        $data['status'] = 'fail';
        $data['message'] = 'Invalid parameters or parameters missing.';
        echo json_encode($data);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if(isset($_GET['id'])) {
        $id = mysqli_real_escape_string($conn, $_GET['id']);
        $sql = "DELETE FROM users WHERE id='$id'";
        if (mysqli_query($conn, $sql)) {
            http_response_code(200);
            $data['status'] = 'success';
            $data['message'] = 'User has been deleted successfully.';
            echo json_encode($data);
        } else {
            http_response_code(500);
            $data['status'] = 'fail';
            $data['message'] = 'Some error occured please try again after sometime.';
            echo json_encode($data);
        }
    } else {
        http_response_code(500);
        $data['status'] = 'fail';
        $data['message'] = 'Invalid parameters or parameters missing.';
        echo json_encode($data);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "Select * from users";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $data['data'][] = $row;            
            }
        } else {
            $data['data'] = []; 
            $data['message'] = 'No record found.';
        }
        http_response_code(200);
        $data['status'] = 'success';
        $data['message'] = 'Data fetched successfully.';
        echo json_encode($data);
    } else {
        http_response_code(500);
        $data['status'] = 'fail';
        $data['message'] = 'Some error occured please try again after sometime.';
        echo json_encode($data);
    }
}
