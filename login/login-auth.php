<?php
session_start();
include('../database/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identifier = $_POST['identifier'];
    $password = $_POST['password'];

    if (empty($identifier) || empty($password)) {
        $error = "Please fill in both fields!";
        header('Location: ../login-page.php?error=' . urlencode($error));
        exit;
    }

    // Fetch the user by username or email
    $stmt = $conn->prepare("SELECT * FROM users_tbl WHERE username = :identifier OR email = :identifier");
    $stmt->execute(['identifier' => $identifier]);    
    $user = $stmt->fetch();

    if ($user) {
        $hashed_password_from_db = $user['password'];

        if (password_verify($password, $hashed_password_from_db)) { 
            $_SESSION['logged_in'] = true;
            $_SESSION['user'] = $user;

            $user_type = $user['user_type'];

            // Redirect based on user type
            if ($user_type == 'admin') {
                header('Location: ../admin/admin-page.php');
            } else {
                header('Location: ../home.php');
            }
            exit;
        } else {
            $error = "Invalid username or password!";
            header('Location: ../login-page.php?error=' . urlencode($error));
            exit;
        }
    } else {
        $error = "Invalid username or password!";
        header('Location: ../login-page.php?error=' . urlencode($error));
        exit;
    }
} else {
    header('Location: ../login-page.php');
    exit;
}
?>
