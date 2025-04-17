<?php
session_start();
include('../database/db.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(403);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user']['user_id'];
    $field = $_POST['field'] ?? '';
    $value = $_POST['value'] ?? '';

    $allowedFields = ['fullname', 'username', 'email', 'phone'];
    if (!in_array($field, $allowedFields)) {
        http_response_code(400);
        echo 'Invalid field.';
        exit;
    }

    // Special handling for fullname
    if ($field === 'fullname') {
        $firstname = trim($_POST['firstname'] ?? '');
        $lastname = trim($_POST['lastname'] ?? '');
        if ($firstname === '' || $lastname === '') {
            http_response_code(400);
            echo 'First name and last name are required.';
            exit;
        }

        $value = $firstname . ' ' . $lastname;
    }

    $sql = "UPDATE users_tbl SET $field = :value WHERE user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':value', $value);
    $stmt->bindParam(':user_id', $user_id);

    if ($stmt->execute()) {
        $_SESSION['user'][$field] = $value;
        echo 'success';
    } else {
        http_response_code(500);
        echo 'Update failed.';
    }
}
?>
