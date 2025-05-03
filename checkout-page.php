<?php
session_start();
require_once 'database/db.php';

$isCartCheckout = isset($_GET['from']) && $_GET['from'] === 'cart';
$product = null;

if (!$isCartCheckout && isset($_POST['id']) && is_numeric($_POST['id'])) {
    $product_id = intval($_POST['id']);

    $sql = "SELECT p.*, c.category_name 
            FROM product_tbl p 
            LEFT JOIN category_tbl c ON p.category_id = c.category_id
            WHERE p.product_id = :product_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "Product not found.";
        exit;
    }
} elseif (!$isCartCheckout) {
    echo "Invalid product ID.";
    exit;
}

$user_id = $_SESSION['user']['user_id'] ?? null;
$addresses = [];

if ($user_id) {
    $sql = "SELECT * FROM address_tbl WHERE user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "User ID not found in session.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - <?= $isCartCheckout ? "Your Cart" : htmlspecialchars($product['product_name']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Fonts & Styles -->
    <link href="https://fonts.googleapis.com/css2?family=Afacad&family=Caveat&family=Dosis&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/checkout-page.css">
</head>
<body>

<nav class="custom-navbar">
    <div class="logo">
        <img src="assets/web_img/ak-logo.png?v2" alt="Adorable Knots Logo">
    </div>
    <div class="nav-links">
        <button><img src="assets/icons/home.png" alt=""><a href="home.php">Home</a></button>
        <button class="active"><img src="assets/icons/bag.png" alt=""><a href="store-page.php">Shop Now</a></button>
        <button><img src="assets/icons/order.png" alt=""><a href="<?= isset($_SESSION['logged_in']) ? 'orders-page.php' : 'signup-page.php' ?>">My Orders</a></button>
        <button><img src="assets/icons/user.png" alt=""><a href="<?= isset($_SESSION['logged_in']) ? 'account-page.php' : 'signup-page.php' ?>">Account</a></button>
        <button><img src="assets/icons/cart.png" alt=""><a href="<?= isset($_SESSION['logged_in']) ? 'cart-page.php' : 'signup-page.php' ?>">Cart</a></button>
    </div>
</nav>

<div class="product-container">
    <form action="process_checkout.php" method="POST" class="product-box">
        <!-- Product Display -->
        <div class="product-details-container">
            <?php if (!$isCartCheckout): ?>
                <img src="uploads/<?= htmlspecialchars($product['product_img']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
                <div class="product-details-content">
                    <div class="product-name"><?= htmlspecialchars($product['product_name']) ?></div>
                    <div class="product-price"><strong>Price:</strong> ₱<?= number_format($product['product_price'], 2) ?></div>
                    <div class="product-description"><?= nl2br(htmlspecialchars($product['product_description'])) ?></div>
                    <div class="form-group estimated-delivery-group">
                        <span class="estimated-delivery-label"><strong>Estimated Delivery:</strong></span>
                        <span class="estimated-delivery-text"><?= htmlspecialchars($product['estimated_delivery']) ?></span>
                    </div>
                </div>
            <?php else: ?>
                <div class="product-details-content w-100">
                    <?php
                    foreach ($_SESSION['cart'] ?? [] as $productId => $quantity) {
                        $stmt = $conn->prepare("SELECT * FROM product_tbl WHERE product_id = ?");
                        $stmt->execute([$productId]);
                        $item = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($item):
                    ?>
                        <div class="cart-item">
                            <img src="uploads/<?= htmlspecialchars($item['product_img']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>">
                            <div>
                                <div class="product-name fw-bold"><?= htmlspecialchars($item['product_name']) ?></div>
                                <div class="product-price">₱<?= number_format($item['product_price'], 2) ?> x <?= $quantity ?></div>
                                <div class="product-description"><?= nl2br(htmlspecialchars($item['product_description'])) ?></div>
                                <div class="estimated-delivery"><strong>Estimated Delivery:</strong> <?= htmlspecialchars($item['estimated_delivery']) ?></div>
                            </div>
                        </div>
                    <?php
                        endif;
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Checkout Inputs -->
        <div class="product-checkout-inputs">
            <?php if (!$isCartCheckout): ?>
                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
                <input type="hidden" name="product_price" value="<?= htmlspecialchars($product['product_price']) ?>">
            <?php else: ?>
                <input type="hidden" name="from_cart" value="1">
            <?php endif; ?>

            <input type="hidden" name="customization" id="customizationInput" value="">

            <!-- Address Select -->
            <?php if (!empty($addresses)): ?>
                <div class="form-group">
                    <label for="addressSelect"><strong>Select Delivery Address:</strong></label>
                    <select id="addressSelect" name="address_id" class="form-control" required>
                        <option value="">-- Choose an address --</option>
                        <?php foreach ($addresses as $address): ?>
                            <option value="<?= htmlspecialchars($address['address_id']) ?>">
                                <?= htmlspecialchars($address['street_details'] . ', ' . $address['barangay'] . ', ' . $address['city'] . ', ' . $address['province'] . ', ' . $address['postal_code']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php else: ?>
                <p style="color: red;"><strong>No addresses found.</strong> Please add one from your <a href="account-page.php">Account Page</a>.</p>
            <?php endif; ?>

            <!-- Payment Method -->
            <div class="form-group">
                <label for="payment_mode"><strong>Payment Method:</strong></label>
                <select name="payment_mode" id="payment_mode" class="form-control" required>
                    <option value="">-- Select payment method --</option>
                    <option value="COD">Cash on Delivery (COD)</option>
                    <option value="GCash">GCash</option>
                </select>
            </div>

            <!-- Quantity -->
            <?php if (!$isCartCheckout): ?>
                <div class="quantity-container my-3 d-flex align-items-center justify-content-center gap-2">
                    <label for="productQuantity" class="form-label mb-0"><strong>Quantity:</strong></label>
                    <input type="number" id="productQuantity" name="quantity" class="form-control text-center" value="1" min="1" style="width: 80px;">
                </div>
            <?php endif; ?>

            <!-- Customization -->
            <div class="product-customizations">
                <label for="customization"><strong>Message (Optional):</strong></label>
                <textarea id="customization" name="customizationText" rows="3" placeholder="Type your customization/request here..." style="width: 100%; height: 150px; resize: vertical;"></textarea>
            </div>

            <button id="checkoutBtn" class="btn mt-3" style="background-color: #FF7EBC; color: white;">Checkout</button>
        </div>
    </form>
</div>

<script>
    document.querySelector('form').addEventListener('submit', function () {
        const customizationText = document.getElementById('customization').value;
        document.getElementById('customizationInput').value = customizationText;
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="javascript/navbar-icons.js"></script>
</body>
</html>
