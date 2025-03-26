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
        <div class="nav-links">
            <a href="#">SIGN UP</a>
            <p>|</p>
            <a href="#">LOGIN</a>
        </div>
        <div class="container1">
            <div class="logo">
                <img src="assets/web_img/ak_logo.png" alt="Adorable Knots Logo">
            </div>
            <div class="search-bar">
                <input type="text" placeholder="Search...">
                <button><img src="assets/web_img/search.png" alt="Search"></button>
            </div>
            <div class="cart-btn">
                <button><img src="assets/web_img/shopping-cart.png" alt="Cart"> Cart</button>
            </div>

        
        </div>
    </nav>
    <section class="banner-section">
        <img src="assets/web_img/banner_bg.png" alt="Banner Image" class="banner-img">
        <div class="banner-overlay"></div>
        <div class="banner-content">
            <h1>SOFTLY CRAFTED</h1>
            <p>CRAFTED BY HAND, TREASURED WITH LOVE.</p>
            <button class="banner-btn">
                Visit our Store 
                <!-- <img src="assets/web_img/shopping-cart.png" alt="Cart Icon"> -->
            </button>
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

    <section class="collections-section">
    <button class="view-all-btn">View All Products</button>
        <h3>Explore <span>Our Collections</span></h3>
        <div class="collections-slider">
            <button class="slider-btn left">&lt;</button>
            <div class="collection">
                <img src="assets/web_img/flowers.png" alt="Flowers">
                <p>FLOWERS</p>
            </div>
            <div class="collection">
                <img src="assets/web_img/accessories.png" alt="Accessories">
                <p>ACCESSORIES</p>
            </div>
            <div class="collection">
                <img src="assets/web_img/wearables.png" alt="Wearables">
                <p>WEARABLES</p>
            </div>
            <button class="slider-btn right">&gt;</button>
        </div>
    </section>


</body>
</html>
