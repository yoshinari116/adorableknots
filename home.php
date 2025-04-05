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
    <link href="https://fonts.googleapis.com/css2?family=Afacad:ital,wght@0,400..700;1,400..700&family=Dosis:wght@200..800&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="container1">
            <div class="logo">
                <img src="assets/web_img/ak_logo.png" alt="Adorable Knots Logo">
            </div>
            <div class="search-bar">
                <input type="text" placeholder="Search for a product">
                <button><img src="assets/web_img/search.png" alt="Search"></button>
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
        </div>
        <div class= "nav-divider"></div>
        <div class="nav-links">
            <a href="home.php"><p>HOME</p></a>
            <a href="#"><p>ABOUT</p></a>
            <a href="#"><p>PRODUCTS</p></a>
            <a href="#"><p>CONTACT</p></a>
            <a href="#"><p>FOLLOW US</p></a>
        </div>
    </nav>
    <section class="banner-section">
        <div class="banner-bg">

        </div>
        <div class="banner-content">
            <h1>SOFTLY CRAFTED</h1>
            <p>CRAFTED BY HAND, TREASURED WITH LOVE.</p>
        </div>
        <div class="banner-btn"> 
            <button>Visit our Store<img src="assets/web_img/cart.png" alt=""></button>
        </div>
    </section>

    <section class="intro-section">
        <div class="intro-content">
            <h2>WELCOME TO ADORABLE KNOTS</h2>
            <p>
                Adorable Knots is your go-to for handcrafted crochet bouquets, apparel, and accessories.
                Each piece is made with care, offering unique and eco-friendly designs. Explore our collection
                or customize something special. We can't wait to create for you!
            </p>
        </div>
        <div class="intro-images">
            <img src="assets/web_img/yarns_intro.png" alt="Yarn Collection">
        </div>
    </section>

    <section class="collection-section">
        <div class="collection-header">
            <div class="collection-header-child">
                <div class="collection-title">Explore Our Collections</div>
                <div class="collection-underline"></div>
            </div>
            <button>View All Products</button>
        </div>
        <div class="category-slider-container">
            <div class="category-slider">
                <button class="prev-btn"><img src="assets/web_img/left-arrow.png" alt=""></button>

                <div class="slider-wrapper"> <!-- New wrapper for overflow control -->
                    <div class="slider">
                        <div class="category-card">
                            <div class="category-image"></div>
                            <p class="category-title">FLOWERS</p>
                        </div>
                        <div class="category-card">
                            <div class="category-image"></div>
                            <p class="category-title">ACCESSORIES</p>
                        </div>
                        <div class="category-card">
                            <div class="category-image"></div>
                            <p class="category-title">HANDMADE TOYS & DECOR</p>
                        </div>
                        <div class="category-card">
                            <div class="category-image"></div>
                            <p class="category-title">CLOTHING & WEARABLES</p>
                        </div>
                        <div class="category-card">
                            <div class="category-image"></div>
                            <p class="category-title">OTHERS</p>
                        </div>
                    </div>
                </div>

                <button class="next-btn"><img src="assets/web_img/right-arrow.png" alt=""></button>
            </div>
        </div>
    </section>
    <script src="javascript/slider.js"></script>

</body>
</html>
