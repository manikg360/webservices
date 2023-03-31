<?php

require_once 'config.php';

// Manage Groups
header('Content-Type: application/json; charset=utf-8');
$postData = json_decode(file_get_contents('php://input'), true);
$data =[];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($postData['name'])) {
        $name = mysqli_real_escape_string($conn, $postData['name']);
        $sql = "INSERT INTO `groups` (name) VALUES ('$name')";
        if (mysqli_query($conn, $sql)) {
            $last_inserted_id = mysqli_insert_id($conn);
            // Query the database to retrieve the last inserted record
            $query = "SELECT * FROM `groups` WHERE id = $last_inserted_id";
            $result = mysqli_query($conn, $query);
            // Fetch the record as an associative array
            $record = mysqli_fetch_assoc($result);
            http_response_code(201);
            $data['data'] = $record;
            $data['status'] = 'success';
            $data['message'] = 'Group created successfully.';
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
    } else if(isset($_GET['id']) && isset($postData['user_id'])) {
        $group_id = mysqli_real_escape_string($conn, $_GET['id']);
        $user_id = mysqli_real_escape_string($conn, $postData['user_id']);
        $sql = "INSERT INTO group_members (group_id, user_id) VALUES ('$group_id', '$user_id')";
        if (mysqli_query($conn, $sql)) {
            $last_inserted_id = mysqli_insert_id($conn);
            // Query the database to retrieve the last inserted record
            $query = "SELECT * FROM `group_members` WHERE id = $last_inserted_id";
            $result = mysqli_query($conn, $query);
            // Fetch the record as an associative array
            $record = mysqli_fetch_assoc($result);
            http_response_code(201);
            $data['data'] = $record;
            $data['status'] = 'success';
            $data['message'] = 'Member has been added to group successfully.';
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

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if(isset($_GET['id'])) {
        $id = mysqli_real_escape_string($conn, $_GET['id']);
        $sql = "DELETE FROM `groups` WHERE id='$id'";
        if (mysqli_query($conn, $sql)) {
            http_response_code(200);
            $data['status'] = 'success';
            $data['message'] = 'Group has been deleted successfully.';
            echo json_encode($data);
        } else {
            http_response_code(500);
            $data['status'] = 'fail';
            $data['message'] = 'Some error occurred. Please try again after sometime.';
            echo json_encode($data);
        }
    } else {
        http_response_code(500);
        $data['status'] = 'fail';
        $data['message'] = 'Invalid/Missing parameters.';
        echo json_encode($data);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $condition = 'WHERE 1';
    if(isset($_GET['q'])) {
        $q = mysqli_real_escape_string($conn, $_GET['q']);
        $condition .= " AND name LIKE '%$q%'";
    }
    $sql = "SELECT * FROM `groups` $condition";
    $result = mysqli_query($conn, $sql);
    $groups = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $data['data'][] = $row;
    }
    http_response_code(200);
    $data['status'] = 'success';
    $data['message'] = 'Data fetched successfully.';
    echo json_encode($data);
}
