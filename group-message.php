<?php

require_once 'config.php';

// Group Messages
header('Content-Type: application/json; charset=utf-8');
$postData = json_decode(file_get_contents('php://input'), true);
$data =[];
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        http_response_code(401);
        $data['status'] = 'fail';
        $data['message'] = 'Unauthenticated User.';
        echo json_encode($data);exit;
    }
    if(isset($_GET['id']) && isset($postData['message'])) {
        $group_id = mysqli_real_escape_string($conn, $_GET['id']);
        $user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);
        $message = mysqli_real_escape_string($conn, $postData['message']);
        $sql = "INSERT INTO group_messages (group_id, user_id, message) VALUES ('$group_id', '$user_id', '$message')";
        if (mysqli_query($conn, $sql)) {
            $last_inserted_id = mysqli_insert_id($conn);
            // Query the database to retrieve the last inserted record
            $query = "SELECT * FROM `group_messages` WHERE id = $last_inserted_id";
            $result = mysqli_query($conn, $query);
            // Fetch the record as an associative array
            $record = mysqli_fetch_assoc($result);
            http_response_code(201);
            $data['data'] = $record;
            $data['status'] = 'success';
            $data['message'] = 'Message has been added to group successfully.';
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
    } else if (isset($_GET['id']) && isset($_GET['message_id']) && isset($postData['like'])) {
        $group_id = mysqli_real_escape_string($conn, $_GET['id']);
        $user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);
        $message_id = mysqli_real_escape_string($conn, $_GET['message_id']);
        $like = mysqli_real_escape_string($conn, $postData['like']);
        $sql = "INSERT INTO message_likes (group_id, user_id, message_id, is_like) VALUES ('$group_id', '$user_id', '$message_id', '$like')";
        if (mysqli_query($conn, $sql)) {
            $last_inserted_id = mysqli_insert_id($conn);
            // Query the database to retrieve the last inserted record
            $query = "SELECT * FROM `message_likes` WHERE id = $last_inserted_id";
            $result = mysqli_query($conn, $query);
            // Fetch the record as an associative array
            $record = mysqli_fetch_assoc($result);
            http_response_code(201);
            $data['data'] = $record;
            $data['status'] = 'success';
            $data['message'] = 'Likes added successfully.';
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
        $data['message'] = 'Invalid/Missing parameters.';
        echo json_encode($data);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $group_id = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "SELECT gm.id, gm.message, u.name AS user_name, COUNT(ml.is_like) AS likes FROM group_messages gm
            JOIN users u ON u.id=gm.user_id
            LEFT JOIN message_likes ml ON ml.message_id=gm.id AND ml.is_like=1
            WHERE gm.group_id='$group_id'
            GROUP BY gm.id, gm.message, u.name
            ORDER BY gm.id";
    $result = mysqli_query($conn, $sql);
    $messages = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $row['is_liked'] = false;
        if ($_SESSION['user_id']) {
            $message_id = $row['id'];
            $user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);
            $sql = "SELECT is_like FROM message_likes WHERE group_id='$group_id' AND user_id='$user_id' AND message_id='$message_id'";
            $like_result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($like_result) > 0) {
                $like = mysqli_fetch_assoc($like_result);
                $row['is_liked'] = $like['is_like'];
            }
        }
        $messages[] = $row;
    }
    $data['data'] = $messages;
    $data['status'] = 'success';
    $data['message'] = 'Liked fetched successfully.';
    echo json_encode($data);
}
