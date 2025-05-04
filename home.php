<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adorable Knots</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Afacad:ital,wght@0,400..700;1,400..700&family=Caveat:wght@400..700&family=Dosis:wght@200..800&display=swap" rel="stylesheet">


    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">

    <!-- Styles -->
    <link rel="stylesheet" href="css/home.css">
</head>

<body>
    <nav class="custom-navbar">
      
        <div class="logo">
            <img src="assets/web_img/ak-logo.png?v2" alt="Adorable Knots Logo">
        </div>

        <div class="nav-links">
            <button class="active"><img src="assets/icons/home.png"><a href="home.php">Home</a></button>
            <button><img src="assets/icons/bag.png"><a href="store-page.php">Shop Now</a></button>
            <button><img src="assets/icons/order.png"><a href="<?= isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'orders-page.php' : 'signup-page.php' ?>">My Orders</a></button>
            <button><img src="assets/icons/user.png"><a href="<?= isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'account-page.php' : 'signup-page.php' ?>">Account</a></button>
            <button><img src="assets/icons/cart.png"><a href="<?= isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'cart-page.php' : 'signup-page.php' ?>">Cart</a></button>
        </div>
        
      
    </nav>

    <section class="banner-section">
        <div class="banner-bg"></div>
        <div class="banner-content">SOFTLY CRAFTED
            <div class="banner-sub-text">CRAFTED BY HAND, TREASURED WITH LOVE.</div>
            <button>
                <a href="store-page.php">Visit Our Store</a>
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
            <button>
                <a href="store-page.php">Show All Products</a>
            </button>
        </div>
        <div class="collection-container">
            <div class="collection-highlight-container">
                <div class="collection-highlight-img-A"></div>
                <div class="collection-highlight-content-A">Crafted with Care from Petals to Plush, <br>Made to Warm Your Heart</div>
            </div>
            <div class="collection-cards-container">
                <div class="collection-card">
                    <img src="assets/web_img/solo-flower.png" alt="">
                    <div class="collection-card-name">Individual Flowers</div>
                    <div class="collection-card-line"></div>
                </div>
                <div class="collection-card">
                <img src="assets/web_img/flower-bouquet.png" alt="">
                    <div class="collection-card-name">Whimsy Bouquets</div>
                    <div class="collection-card-line"></div>
                </div>
                <div class="collection-card">
                    <img src="assets/web_img/amigurumi.png" alt="">
                    <div class="collection-card-name">Amigurumis</div>
                    <div class="collection-card-line"></div>
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
                <div class="collection-title">Stiched Styles</div>
                <div class="collection-line"></div>
            </div>
            <button>
              <a href="store-page.php">Show All Products</a>
            </button>
        </div>
        <div class="collection-container">
             <div class="collection-highlight-container">
                <div class="collection-highlight-img-B"></div>
                <div class="collection-highlight-content-B">Woven with Purpose from Loops to Looks, <br>Crafted to Complete Your Style</div>
            </div>
            <div class="collection-cards-container">

                <div class="collection-card">
                    <img src="assets/web_img/bag-wallet.png" alt="">
                    <div class="collection-card-name">Bags & Wallet</div>
                    <div class="collection-card-line"></div>
                </div>

                <div class="collection-card">
                    <img src="assets/web_img/wearables.png?v1" alt="">
                    <div class="collection-card-name">Accessories & Wearables</div>
                    <div class="collection-card-line"></div>
                </div>

                <div class="collection-card">
                    <img src="assets/web_img/keychains.png" alt="">
                    <div class="collection-card-name">Knitted Keychains</div>
                    <div class="collection-card-line"></div>
                </div>

                
            </div>
        </div>
    </section>

    <div class="quote"><p>"Every stitch is made with love, every knot weaves a story, and every creation is a timeless piece crafted just for you."</p></div>
        
    <section class="footer">
        <div class="footer-others">
            <p> Shipping: Nationwide via J&T / LBC</p>
            <p> Made to order: Crafting starts after you place an order </p>
            <p> Support: DM us anytime on FB or IG</p>
        </div>
        <div class="footer-line"></div>
        
        <div class="footer-info">
            <div class="footer-logo">
            <img src="assets/web_img/ak-logo.png" alt="Adorable Knots Logo">
            </div>
            <div class="footer-content">
                <div class="footer-content-header">Contact</div>
                <p>alexandrajingco352@gmail.com<br>0919 123 4567</p>
            </div>
            <div class="footer-content">
                <div class="footer-content-header">Address</div>
                <p>Phase 2 Block 5 Lot 1 <br> Magdalena Homes <br>
                Sto. Tomas, Subic, Zambales</p>
            </div>
            <div class="footer-content">
                <div class="footer-content-header">Follow Us</div>
                <p>
                    <a href="#"><img src="assets/icons/facebook.png" alt="">Facebook</a>
                    <a href="#"><img src="assets/icons/instagram.png" alt="">Instagram</a>
                </p>  
            </div>
        </div> 
        
       
        
    </section>
    
    <script src="javascript/burger-bar.js"></script>
    <script src="javascript/navbar-icons.js"></script>

</body>
</html>

