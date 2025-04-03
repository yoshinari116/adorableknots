<?php
session_start();
include('../database/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare(query: "SELECT * FROM users_tbl WHERE BINARY username = :username AND password = :password");
    $stmt->execute(params: ['username' => $username, 'password' => $password]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['logged_in'] = true;
        $_SESSION['user'] = $user;

        $role = $user['role'];
       
        if($role == 'admin'){
            header(header: 'Location: ../admin/admin-page.php');
        }else{
            header(header: 'Location: ../store-page.php');
        }exit;


        header(header: 'Location: ../store-page.php');
        exit;
    }
    else{
        $error = "Invalid username or password!";
        header(header: 'Location: ../login-page.php?error=' . $error);
        exit;
    }
} else{
    header(header: 'Location: ../login-page.php');
    exit;
}
?>