<?php
session_start();
include '../database/db.php';  // Adjusted include path to go one level up to access db.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $user_type = 'user'; // default type

    if ($password !== $confirm_password) {
        echo "Passwords do not match.";
        exit();
    }

    $fullname = $first_name . ' ' . $last_name;
    $username = $first_name;

    // Generate user_id: YYMM + 4-digit random number
    do {
        $user_id = date('y') . date('m') . str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
        
        // Check if user_id already exists
        $stmt = $conn->prepare("SELECT * FROM users_tbl WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
    } while ($stmt->rowCount() > 0);

    // Insert new user
    $sql = "INSERT INTO users_tbl (user_id, fullname, username, email, phone, password, user_type) 
            VALUES (:user_id, :fullname, :username, :email, :phone, :password, :user_type)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':fullname', $fullname);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT)); // Hash password
    $stmt->bindParam(':user_type', $user_type);

    if ($stmt->execute()) {
        header("Location: ../login-page.php");
        exit();
    } else {
        echo "Error: " . $stmt->errorInfo()[2];
    }
}
?>

