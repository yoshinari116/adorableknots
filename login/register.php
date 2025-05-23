<?php
session_start();
include('../database/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Invalid email format!';
        header('Location: ../signup-page.php');
        exit;
    }

    // Check if the username already exists
    $stmt = $conn->prepare("SELECT * FROM users_tbl WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $existing_user = $stmt->fetch();

    if ($existing_user) {
        // Username already exists
        $_SESSION['error'] = 'Username is already taken!';
        header('Location: ../signup-page.php');
        exit;
    }

    // Check if the email already exists
    $stmt = $conn->prepare("SELECT * FROM users_tbl WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $existing_email = $stmt->fetch();

    if ($existing_email) {
        // Email already exists
        $_SESSION['error'] = 'Email is already registered!';
        header('Location: ../signup-page.php');
        exit;
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Passwords do not match!';
        header('Location: ../signup-page.php');
        exit;
    }

    // Validate password length (8-16 characters)
    if (strlen($password) < 8 || strlen($password) > 16) {
        $_SESSION['error'] = 'Password must be between 8 and 16 characters!';
        header('Location: ../signup-page.php');
        exit;
    }

    // Generate user_id in the format UID + YY + MM + Seconds + Random digits
    $uid = 'UID'; // Static part of the UID
    $timestamp = date('y') . date('m') . date('s'); // Format: YYMMSeconds
    $random_digits = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT); // Generate 4 random digits (e.g., 0234)
    $user_id = $uid . $timestamp . $random_digits;

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $user_type = 'user';

    // Insert into the database
    $stmt = $conn->prepare("INSERT INTO users_tbl (user_id, username, email, fullname, password, user_type) VALUES (:user_id, :username, :email, :fullname, :password, :user_type)");
    $stmt->execute([
        'user_id' => $user_id,
        'username' => $username,
        'email' => $email,
        'fullname' => $first_name . ' ' . $last_name, // Combine first and last name
        'password' => $hashed_password,
        'user_type' => $user_type
    ]);

    $_SESSION['success'] = 'Account created successfully!';
    header('Location: ../login-page.php');
    exit;
}
?>
