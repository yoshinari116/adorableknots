<?php
session_start();
include('../database/db.php');

// Check if the user is logged in
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

    // Update the specified field in the database
    $sql = "UPDATE users_tbl SET $field = :value WHERE user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':value', $value);
    $stmt->bindParam(':user_id', $user_id);

    if ($stmt->execute()) {
        // Fetch the updated user data from the database
        $query = $conn->prepare("SELECT * FROM users_tbl WHERE user_id = :user_id");
        $query->bindParam(':user_id', $user_id);
        $query->execute();
        $updatedUser = $query->fetch(PDO::FETCH_ASSOC);

        // Update the session
        $_SESSION['user'] = $updatedUser;

        echo 'Account updated successfully.';
    } else {
        http_response_code(500);
        echo 'Failed to update account.';
    }
}
?>
