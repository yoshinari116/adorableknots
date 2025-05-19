<?php
session_start();
require_once 'database/db.php';

$user_id = $_SESSION['user']['user_id'] ?? null;

if (!$user_id) {
    echo "User not logged in.";
    exit;
}

// Fetch user addresses
$sql = "SELECT * FROM address_tbl WHERE user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
$stmt->execute();
$addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Determine if this is a single item or multiple items checkout
$products = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id']) && isset($_POST['cart_id'])) {
        // Single item checkout
        $product_id = $_POST['product_id'];
        $cart_id = $_POST['cart_id'];

        $stmt = $conn->prepare("
            SELECT p.product_id, p.product_name, p.product_price, p.product_img, p.product_description, p.estimated_delivery, c.quantity, c.customization, c.cart_id
            FROM cart_tbl c
            JOIN product_tbl p ON c.product_id = p.product_id
            WHERE c.user_id = ? AND c.cart_id = ? AND p.product_id = ?
        ");
        $stmt->execute([$user_id, $cart_id, $product_id]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } elseif (isset($_POST['product_ids'])) {
        // Checkout all selected products
        $productIds = explode(',', $_POST['product_ids']);
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));

        // Prepare SQL with dynamic placeholders
        $sql = "
            SELECT p.product_id, p.product_name, p.product_price, p.product_img, p.product_description, p.estimated_delivery, c.quantity, c.customization, c.cart_id
            FROM cart_tbl c
            JOIN product_tbl p ON c.product_id = p.product_id
            WHERE c.user_id = ? AND p.product_id IN ($placeholders)
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute(array_merge([$user_id], $productIds));
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$_SESSION['cart'] = $products;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Checkout - Cart</title>
  <link href="https://fonts.googleapis.com/css2?family=Afacad&family=Caveat&family=Dosis&display=swap" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="css/home.css"/>
  <link rel="stylesheet" href="css/cart-checkout-page-bulk.css"/>
</head>
<body>
    <nav class="custom-navbar">
        <div class="logo">
            <img src="assets/web_img/ak-logo.png?v2" alt="Adorable Knots Logo">
        </div>
        <div class="nav-links">
            <button><a href="home.php">Home</a></button>
            <button><a href="store-page.php">Shop Now</a></button>
            <button><a href="orders-page.php">My Orders</a></button>
            <button><a href="account-page.php">Account</a></button>
            <button class="active"><a href="cart-page.php">Cart</a></button>
        </div>
    </nav>

    <div class="product-container">
        <form action="orders/cart-process-checkout.php" method="POST" class="product-box">
            <div class="products-list-container">
                <?php foreach ($products as $product): ?>
                    <div class="product-details-container mb-0">
                        <?php if (!empty($product['product_img'])): ?>
                        <img src="uploads/<?= htmlspecialchars($product['product_img']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" class="product-image">
                        <?php else: ?>
                        <p>Image missing for <?= htmlspecialchars($product['product_name']) ?></p>
                        <?php endif; ?>

                        <div class="product-details-content">
                            <div class="product-name"><?= htmlspecialchars($product['product_name']) ?></div>
                            <div class="product-price"><strong>Price:</strong> ₱<?= number_format($product['product_price'], 2) ?></div>
                            <div class="product-description"><?= nl2br(htmlspecialchars($product['product_description'])) ?></div>

                            <div class="form-group estimated-delivery-group">
                                <span class="estimated-delivery-label"><strong>Estimated Delivery:</strong></span>
                                <span class="estimated-delivery-text"><?= htmlspecialchars($product['estimated_delivery']) ?></span>
                            </div>

                            <div class="form-group mt-2">
                                <label><strong>Quantity:</strong></label>
                                <input type="number" name="products[<?= $product['product_id'] ?>][quantity]" value="<?= htmlspecialchars($product['quantity']) ?>" min="1" class="form-control" style="width: 100px;">
                            </div>

                            <div class="form-group mt-2">
                                <label><strong>Customization (optional):</strong></label>
                                <textarea name="products[<?= $product['product_id'] ?>][customization]" rows="3" placeholder="Type your customization/request here..." style="width: 100%; resize: vertical;"><?= htmlspecialchars($product['customization'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <input type="hidden" name="products[<?= $product['product_id'] ?>][cart_id]" value="<?= htmlspecialchars($product['cart_id']) ?>" />
                        <input type="hidden" name="products[<?= $product['product_id'] ?>][product_id]" value="<?= htmlspecialchars($product['product_id']) ?>" />

                    </div>
                <?php endforeach; ?>
            </div>

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

                <button type="submit" id="checkoutBtn" class="btn mt-3" style="background-color: #FF7EBC; color: white;">Checkout</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="javascript/navbar-icons.js"></script>

</body>
</html>
