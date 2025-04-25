<?php
session_start();
include('database/db.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login-page.php');
    exit;
}

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
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

    <!-- Styles -->
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/account-page.css">
    <link rel="stylesheet" href="css/navbar.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Afacad:ital,wght@0,400..700;1,400..700&family=Caveat:wght@400..700&family=Dosis:wght@200..800&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <nav class="custom-navbar">
        <div class="logo">
            <img src="assets/web_img/ak-logo.png?v2" alt="Adorable Knots Logo">
        </div>

        <div class="nav-links">
            <button><img src="assets/icons/home.png"><a href="home.php">Home</a></button>
            <button><img src="assets/icons/bag.png"><a href="store-page.php">Shop Now</a></button>
            <button><img src="assets/icons/Chat.png"><a href="#">Contact Us</a></button>
            <button class="active"><img src="assets/icons/user.png"><a href="account-page.php">Account</a></button>
            <button><img src="assets/icons/cart.png"><a href="#">Cart ( 0 )</a></button>
        </div>
    </nav>

    <div class="logout">
        <a href="login/logout.php">LOGOUT</a>
        <img src="assets/icons/back-white.png" style="transform: scaleX(-1);" alt="">
    </div>

    <div class="container">
        <div class="account-container">
            <div class="account-nav-links">
                <div class="account-nav-links-header">
                    <img src="assets/icons/user.png" alt="">
                    My Account
                </div>
                <button class="nav-button active" onclick="location.href='account-page.php'">Profile</button>
                <button class="nav-button" onclick="location.href='address-page.php'">Address</button>
                <button class="nav-button" onclick="location.href='#'">Change Password</button>
                <button class="nav-button" onclick="location.href='#'">My Purchases</button>
            </div>

            <form class="account-container-form" action="update-account.php" method="post" id="accountForm" enctype="multipart/form-data">

                <!-- FULL NAME DISPLAY ROW -->
                <div class="info-row">
                    <label>Full Name:</label>
                    <div class="input-group">
                        <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" readonly class="readonly-input">
                    </div>
                    <div class="button-group">
                        <button type="button" class="edit-btn" data-field="fullname" data-state="edit">
                            <img src="assets/icons/edit.png" alt="Edit" class="icon-btn">
                        </button>
                        <button type="button" class="save-btn" data-field="fullname" style="display: none;">
                            <img src="assets/icons/save.png" alt="Save" class="icon-btn">
                        </button>
                    </div>
                </div>

                <!-- HIDDEN FIRST & LAST NAME EDIT ROWS -->
                <div id="fullname-edit-wrapper" style="display: none;">
                    <div class="info-row">
                        <label for="firstname">First Name:</label>
                        <div class="input-group">
                            <input type="text" name="firstname" id="firstname" value="">
                        </div>
                    </div>
                    <div class="info-row">
                        <label for="lastname">Last Name:</label>
                        <div class="input-group">
                            <input type="text" name="lastname" id="lastname" value="">
                        </div>
                    </div>
                </div>

                <div class="info-row">
                    <label>Username:</label>
                    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" readonly>
                    <button type="button" class="edit-btn" data-field="username" data-state="edit">
                        <img src="assets/icons/edit.png" alt="Edit" class="icon-btn">
                    </button>
                    <button type="button" class="save-btn" data-field="username" style="display: none;">
                        <img src="assets/icons/save.png" alt="Save" class="icon-btn">
                    </button>
                </div>

                <div class="info-row">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                    <button type="button" class="edit-btn" data-field="email" data-state="edit">
                        <img src="assets/icons/edit.png" alt="Edit" class="icon-btn">
                    </button>
                    <button type="button" class="save-btn" data-field="email" style="display: none;">
                        <img src="assets/icons/save.png" alt="Save" class="icon-btn">
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .icon-btn {
            width: 20px;
            height: 20px;
            vertical-align: middle;
        }
    </style>

    <script src="assets/js/profile-image-preview.js"></script>
    <script src="javascript/account-update.js"></script>
    <script src="javascript/navbar-icons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
