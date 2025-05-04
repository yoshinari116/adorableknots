<?php
session_start();
include('database/db.php');
 
$mode = '';
$success = '';
$error = '';
$token = $_GET['token'] ?? null;
$user = null;
 
// Determine mode
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $mode = 'change';
} elseif ($token) {
    // RESET mode
    $stmt = $conn->prepare("SELECT * FROM users_tbl WHERE reset_token = :token AND token_expiry > NOW()");
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch();
 
    if ($user) {
        $mode = 'reset';
    } else {
        $error = "Invalid or expired token.";
    }
} else {
    header("Location: login-page.php");
    exit;
}
 
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
 
    if (strlen($new_password) < 8 || strlen($new_password) > 16) {
        $error = "Password must be between 8 and 16 characters.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $updateQuery = "UPDATE users_tbl SET password = :password WHERE user_id = :id";
        $stmt = $conn->prepare($updateQuery);
        $success = $stmt->execute([
            'password' => $hashed_password,
            'id' => $user['user_id']
        ]) ? "Password updated successfully!" : "Failed to update password.";
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $mode === 'reset' ? 'Reset Password' : 'Change Password' ?></title>

    <!-- Styles -->
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/change-password-page.css">
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
            <button><img src="assets/icons/order.png"><a href="<?= isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'orders-page.php' : 'signup-page.php' ?>">My Orders</a></button>
            <button class="active"><img src="assets/icons/user.png"><a href="<?= isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'account-page.php' : 'signup-page.php' ?>">Account</a></button>
            <button><img src="assets/icons/cart.png"><a href="<?= isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'cart-page.php' : 'signup-page.php' ?>">Cart</a></button>
        </div>
    </nav>

    <div class="container">
        
        <div class="password-form-container">
            <div class="account-nav-links">
                <div class="account-nav-links-header">
                    <img src="assets/icons/user.png" alt="">
                    My Account
                </div>
                <button class="nav-button" onclick="location.href='account-page.php'">Profile</button>
                <button class="nav-button" onclick="location.href='address-page.php'">Address</button>
                <button class="nav-button active" onclick="location.href='change-password.page.php'">Change Password</button>
            </div>

            <div class="password-inputs-container">
                <h2><?= $mode === 'reset' ? 'Reset Your Password' : 'Change Your Password' ?></h2>
 
                <?php if ($error): ?>
                    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
                <?php elseif ($success): ?>
                    <p style="color: green;"><?= htmlspecialchars($success) ?></p>
                <?php endif; ?>
        
                <?php if (!$success): ?>
                    <form method="POST" action="">
                        <label>New Password:</label>
                        <input type="password" name="new_password" required>
        
                        <label>Confirm Password:</label>
                        <input type="password" name="confirm_password" required>
        
                        <button class="btn save-pass" type="submit"><?= $mode === 'reset' ? 'Reset Password' : 'Change Password' ?></button>
                    </form>
                <?php endif; ?>
            </div>
             
        </div>
       
    </div>
    <script src="javascript/navbar-icons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>