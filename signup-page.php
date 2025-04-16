<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/signup-page.css">
    <title>Sign Up - Adorable Knots</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Afacad:ital,wght@0,400..700;1,400..700&family=Dosis:wght@200..800&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="Back">
        <a href="home.php">BACK TO HOME</a>
        <img src="assets/web_img/back-white.png" alt="">
    </div>
    <div class="container">
        <div class="signup-header">SIGN UP</div>
        <div class="signup-container">
            <div class="signup-form">
                <form action="register.php" method="POST">
                    <input type="text" name="first_name" placeholder="First Name" required>
                    <input type="text" name="last_name" placeholder="Last Name" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    <button type="submit">SUBMIT</button>
                </form>
                <div class="divider">
                    <p>OR</p>
                </div>
               <div class="login-suggestion">
                Already have an account?<a href="login-page.php">Log In</a>
               </div>
            </div>
            <div class="info-section">
                <div class="logo">
                    <img src="assets/web_img/ak-logo.png?v3" alt="Adorable Knots Logo">
                </div>
                <p>Sign up now to explore our beautiful handmade collections and create something special just for you.</p>
            </div>
        </div>
    </div>
</body>
</html>
