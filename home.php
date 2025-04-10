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
        <div class="nav-container">
            <div class="burger" id="burger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>

        <div class="nav-container">
            <div class="logo">
                <img src="assets/web_img/ak_logo.png" alt="Adorable Knots Logo">
            </div>
        </div>

        <div class="nav-container">
            <button class="account">
                <a href="signup-page.php">ACCOUNT</a>
                <img src="assets/web_img/user.png" alt="">
            </button>
            <button class="cart">
                <a href="#">CART (0) </a>
                <img src="assets/web_img/shopping-cart.png" alt="Cart">
            </button>
        </div>
        
      

    </nav>

    <div class="dropdown-menu" id="dropdownMenu">
        <a href="home.php">HOME</a>
        <a href="#">SHOP</a>
        <a href="#">CONTACT</a>
        <a href="#">FOLLOW US</a>
    </div>

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
                <div class="collection-line"></div>
            </div>
            <button>Show All Products</button>
        </div>
        <div class="collection-images-container">
            <div class="big-img">
                <!-- <img src="../assets/web_img/set-A-overlay.png" alt=""> -->
                <div class="big-img-bg-top"></div>
                <div class="big-img-content">Bloom Threads, <br>Soft Touch, <br>Pure Joy</div>
            </div>

            <div class="collection-group-container">
                <div class="collection-group">
                    <div class="collection-card">
                        <img src="assets/web_img/flower.png" alt="">
                        <div class="collection-card-name">Flowers</div>
                        <a href="#">View</a>
                    </div>
                    <div class="collection-card">
                    <img src="assets/web_img/bouquet.png" alt="">
                        <div class="collection-card-name">Flower Bouquets</div>
                        <a href="#">View</a>
                    </div>
                </div>
                <div class="collection-group">
                    <div class="collection-card">
                        <img src="assets/web_img/bouquet-money.png" alt="">
                        <div class="collection-card-name">Money Bouquets</div>
                        <a href="#">View</a>
                    </div>
                    <div class="collection-card">
                        <img src="assets/web_img/amigurumis.png" alt="">
                        <div class="collection-card-name">Amigurumi</div>
                        <a href="#">View</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="overview">
        <div class="overview-bg"></div>
        <div class="overview-content">
            <img src="assets/web_img/flower-icon.png" alt="">
            <div class="overview-card">
                <div class="overview-card-title">Everlasting Blooms</div>
                <div class="overview-card-line"></div>
                <div class="overview-card-desc">Our crochet flowers are made to stay vibrant for years to come</div>
            </div>
        </div>

        <div class="overview-content">
            <img src="assets/web_img/recycle-icon.png" alt="">
            <div class="overview-card">
                <div class="overview-card-title">Sustainable Crafting</div>
                <div class="overview-card-line"></div>
                <div class="overview-card-desc">We use Eco-friendly materials for a greener future</div>
            </div>
        </div>

        <div class="overview-content">
            <img src="assets/web_img/yarn-ball-icon.png" alt="">
            <div class="overview-card">
                <div class="overview-card-title">Handmade Just for You</div>
                <div class="overview-card-line"></div>
                <div class="overview-card-desc">Each piece is carefully handmade after every order is placed</div>
            </div>
        </div>

    </section>

    <section class="collection-section">
        <div class="collection-header">
            <div class="collection-header-child">
                <div class="collection-title">Stitched Styles</div>
                <div class="collection-line"></div>
            </div>
            <button>Show All Products</button>
        </div>
        <div class="collection-images-container">
            <div class="big-img">
                <!-- <img src="../assets/web_img/set-A-overlay.png" alt=""> -->
                <div class="big-img-bg-bot"></div>
                <div class="big-img-content">Cozy Threads, <br>Soft Weave, <br>Pure Awe</div>
            </div>

            <div class="collection-group-container">
                <div class="collection-group">
                    <div class="collection-card">
                        <img src="assets/web_img/bags.png" alt="">
                        <div class="collection-card-name">Bags</div>
                        <a href="#">View</a>
                    </div>
                    <div class="collection-card">
                    <img src="assets/web_img/flower-bouquet.png" alt="">
                        <div class="collection-card-name">Wallets</div>
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
    
    <script src="javascript/burger-bar.js"></script>

</body>
</html>

