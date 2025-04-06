<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adorable Knots</title>
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
            <img src="assets/web_img/ak_logo.png" alt="Adorable Knots Logo">
        </div>

        <div class="nav-links">
            <a href="home.php">HOME</a>
            <a href="#">PRODUCTS</a>
            <a href="#">CONTACT</a>
            <div class="nav-divider"></div>
            <a href="#">FOLLOW US</a>
        </div>

        <div class="nav-opt">
            <div class="account">
                <img src="assets/web_img/user-icon.png" alt="">
                <a href="login-page.php">Login</a>
                <p>|</p>
                <a href="signup-page.php">Sign Up</a>
            </div>
            <div class="cart-btn">
                <img src="assets/web_img/shopping-cart.png" alt="Cart">
                <a href="#">Cart</a>
            </div>
            
            </div>
    </nav>
    <section class="banner-section">
        <div class="banner-bg"></div>
        <div class="banner-content">SOFTLY CRAFTED
            <div class="banner-sub-text">CRAFTED BY HAND, TREASURED WITH LOVE.</div>
            <button>
                <p>View Our Store</p>
                <img src="assets/web_img/next-circle.png" alt="">
            </button>
        </div>
    </section>

    <section class="intro-section">
        <div class="intro-content">
            <div class="intro-title">WELCOME TO ADORABLE KNOTS</div>
            <div class="intro-desc">
                Adorable Knots is your go-to for handcrafted crochet bouquets, apparel, and accessories.
                Each piece is made with care, offering unique and eco-friendly designs. Explore our collection
                or customize something special. We can't wait to create for you!
            </div>
        </div>
        <div class="intro-image">
            <img src="assets/web_img/yarns_intro.png" alt="Yarn Collection">
        </div>
    </section>

    <section class="collection-section">
        <div class="collection-header">
            <div class="collection-header-child">
                <div class="collection-title">Crochet Creations</div>
                <div class="collection-underline"></div>
            </div>
            <button>Show All Products</button>
        </div>
        <div class="collection-top">
            <div class="big-img">
                <!-- <img src="../assets/web_img/set-A-overlay.png" alt=""> -->
                <div class="big-img-bg"></div>
                <div class="big-img-content">Soft Yarn, Warm Hugs, Love in Loops</div>
            </div>

            <div class="collection-group-container">
                <div class="collection-group">
                    <div class="collection-card">
                        <img src="assets/web_img/solo-flower.png" alt="">
                        <div class="collection-card-name">Flowers</div>
                        <a href="#">View</a>
                    </div>
                    <div class="collection-card">
                    <img src="assets/web_img/flower-bouquet.png" alt="">
                        <div class="collection-card-name">Flower Bouquets</div>
                        <a href="#">View</a>
                    </div>
                </div>
                <div class="collection-group">
                    <div class="collection-card">
                        <img src="assets/web_img/money-bouquet.png" alt="">
                        <div class="collection-card-name">Money Bouquets</div>
                        <a href="#">View</a>
                    </div>
                    <div class="collection-card">
                        <img src="assets/web_img/amigurumi.png" alt="">
                        <div class="collection-card-name">Amigurumi</div>
                        <a href="#">View</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</body>
</html>

