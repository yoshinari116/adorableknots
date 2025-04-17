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

<div class="Back">
    <a href="home.php">BACK TO HOME</a>
    <img src="assets/icons/back-white.png" alt="">
</div>

<div id="errorPopup" class="popup">
    <div class="popup-content">
        <p id="popupMessage"></p>
    </div>
</div>

<style>
    .popup {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: rgba(0, 0, 0, 0.57);
        color: white;
        padding: 10px;
        border-radius: 5px;
        text-align: center;
        font-size: 16px;
        font-weight: bold;
        min-width: 200px;
        z-index: 1000;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
    }
</style>

<script>
    function showPopup(message) {
        var popup = document.getElementById("errorPopup");
        var popupMessage = document.getElementById("popupMessage");
        popupMessage.innerText = message;
        popup.style.display = "block";

        setTimeout(function () {
            popup.style.display = "none";
        }, 3000);
    }

    <?php if (isset($_GET['error'])): ?>
        showPopup("<?php echo $_GET['error']; ?>");
    <?php endif; ?>
</script>


<body>
    <div class="container">
        <div class="login-header">WELCOME BACK!</div>
        <div class="login-form">

            <form action="login/login-auth.php" method="POST">
                <input type="text" name="identifier" placeholder="Username or Email" required autocomplete="off">
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
