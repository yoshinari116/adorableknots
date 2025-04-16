<?php
session_start();
include('database/db.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login-page.php');
    exit;
}

if (isset($_SESSION['user'])) {
    $login_user = $_SESSION['user'];
} else {
    header('Location: login-page.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adorable Knots</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/home.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Afacad:ital,wght@0,400..700;1,400..700&family=Caveat:wght@400..700&family=Dosis:wght@200..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <img src="assets/web_img/ak-logo.png" alt="Adorable Knots Logo">
        </div>

        <div class="nav-links">
            <button> 
                <img src="assets/icons/home.png" alt="">
                <a href="home.php">Home</a>
            </button>
            <button> 
                <img src="assets/icons/bag.png" alt="">
                <a href="store-page.php">Shop Now</a>
            </button>
            <button> 
                <img src="assets/icons/Chat.png" alt="">
                <a href="#">Contact Us</a>
            </button>
            <button class="active"> 
                <img src="assets/icons/user.png" alt="">
                <a href="account-page.php">Account</a>          
            </button>
            <button>
                <img src="assets/icons/cart.png" alt="Cart">
                <a href="#">Cart ( 0 ) </a>
                
            </button>
        </div>
        
      

    </nav>

    <script src="javascript/navbar-icons.js"></script>

</body>
</html>
