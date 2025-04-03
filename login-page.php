<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login-page.css">
    <title>Sign Up - Adorable Knots</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Afacad:ital,wght@0,400..700;1,400..700&family=Dosis:wght@200..800&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="login-header">WELCOME BACK!</div>
        <div class="login-form">

                <?php
            if (isset($_GET['error'])) {
                echo "<p style='color:red; text-align:center;'>" . $_GET['error'] . "</p>";
            
            }

            if (isset($_GET['success'])) {
                echo "<p style='color:green; text-align:center;'>" . $_GET['success'] . "</p>";
            }
            ?>

            <form action="login/login_auth.php" method="POST">
                <input type="username" name="username" placeholder="Username" required autocomplete="off">
                <input type="password" name="password" placeholder="Password" required autocomplete="off">
                <button type="LOGIN">LOGIN</button>
            </form>
            <a href="#  ">Forgot Password?</a>
            <div class="divider">
                <p>OR</p>
            </div>
            <div class="signup-suggestion">
            New to Adorable Knots?<a href="signup-page.php">Sign In</a>
            </div>
        </div>
    </div>
</body>
</html>
