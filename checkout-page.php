<?php
session_start();
require_once 'database/db.php';

$isCartCheckout = isset($_GET['from']) && $_GET['from'] === 'cart';
$products = [];
$user_id = $_SESSION['user']['user_id'] ?? null;
$addresses = [];
$productIds = [];
$quantities = [];

if (isset($_POST['product_ids']) && !empty($_POST['product_ids'])) {    
    $productIds = array_map('trim', $_POST['product_ids']);
    $productIds = array_unique($productIds); // Remove duplicate IDs
    $quantities = $_POST['quantities'] ?? array_fill(0, count($productIds), 1); 
}

if ($isCartCheckout && !empty($productIds)) {
    // Prepare placeholders for the SQL query
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));

    // Prepare and execute the SQL query
    $sql = "SELECT p.*, c.category_name FROM product_tbl p LEFT JOIN category_tbl c ON p.category_id = c.category_id WHERE p.product_id IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    
    try {
        // Execute query with product IDs
        $stmt->execute($productIds);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$products) {
            echo "No products found for checkout.";
            exit;
        }
    } catch (Exception $e) {
        echo "Error executing query: " . $e->getMessage();
        exit;
    }
}
elseif (!$isCartCheckout && isset($_POST['id']) && !empty($_POST['id'])) {
    // Single product checkout
    $product_id = trim($_POST['id']);
    $sql = "SELECT p.*, c.category_name FROM product_tbl p LEFT JOIN category_tbl c ON p.category_id = c.category_id WHERE p.product_id = :product_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "Product not found.";
        exit;
    }
    $products[] = $product;
} else {
    echo "Invalid product ID.";
    exit;
}

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?= $isCartCheckout ? "Your Cart" : htmlspecialchars($products[0]['product_name']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Afacad&family=Caveat&family=Dosis&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="styles.css">
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
        <form action="orders/process-checkout.php" method="POST" class="product-box">
            <?php foreach ($products as $index => $product): ?>
                <div class="product-details-container mb-0">
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

                    <!-- Hidden inputs for product data -->
                    <?php if (!$isCartCheckout): ?>
                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
                    <?php endif; ?>
                    <input type="hidden" name="products[<?= $index ?>][product_price]" value="<?= htmlspecialchars($product['product_price']) ?>">
                    <input type="hidden" name="products[<?= $index ?>][customization]" id="customizationInput<?= $index ?>">
                </div>
            <?php endforeach; ?>

            <div class="product-checkout-inputs">
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

                <div class="form-group">
                    <label for="payment_mode"><strong>Payment Method:</strong></label>
                    <select name="payment_mode" id="payment_mode" class="form-control" required>
                        <option value="">-- Select payment method --</option>
                        <option value="COD">Cash on Delivery (COD)</option>
                        <option value="GCash">GCash</option>
                    </select>
                </div>

                <?php foreach ($products as $index => $product): ?>
                    <div class="quantity-container my-3 d-flex align-items-center justify-content-center gap-2">
                        <label class="form-label mb-0"><strong>Quantity:</strong></label>
                        <input type="number" name="products[<?= $index ?>][quantity]" class="form-control text-center" value="1" min="1" style="width: 80px;">
                    </div>
                <?php endforeach; ?>

                <?php foreach ($products as $index => $product): ?>
                    <div class="product-customizations mb-3">
                        <label><strong>Message (Optional):</strong></label>
                        <textarea rows="3" placeholder="Type your customization/request here..." style="width: 100%; height: 150px; resize: vertical;" 
                            oninput="document.getElementById('customizationInput<?= $index ?>').value = this.value;"></textarea>
                    </div>
                <?php endforeach; ?>

                <?php if ($isCartCheckout): ?>
                    <input type="hidden" name="from_cart" value="1">
                <?php endif; ?>

                <button id="checkoutBtn" class="btn mt-3" style="background-color: #FF7EBC; color: white;">Checkout</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="javascript/navbar-icons.js"></script>
</body>
</html>
