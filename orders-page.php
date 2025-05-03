<?php
session_start();
require_once 'database/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user']['user_id'])) {
    header("Location: signup-page.php");
    exit;
}

$user_id = $_SESSION['user']['user_id'];

// Fetch all orders for the current user
$sql = "SELECT * FROM orders_tbl WHERE user_id = :user_id ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<nav class="custom-navbar">
    <div class="logo">
        <img src="assets/web_img/ak-logo.png?v2" alt="Adorable Knots Logo">
    </div>
    <div class="nav-links">
        <button> 
            <img src="assets/icons/home.png" alt="">
            <a href="home.php">Home</a>
        </button>
        <button> 
            <img src="assets/icons/bag.png" alt="">
            <a href="store-page.php">Shop Now</a>
        </button>
        <button class="active"> 
            <img src="assets/icons/order.png" alt="">
            <a href="orders-page.php">My Orders</a>
        </button>
        <button> 
            <img src="assets/icons/user.png" alt="">
            <a href="account-page.php">Account</a>  
        </button>
        <button>
            <img src="assets/icons/cart.png" alt="Cart">
            <a href="cart-page.php">Cart (0)</a>
        </button>
    </div>
</nav>

<div class="container my-5">
    <h2 class="mb-4">My Orders</h2>

    <?php if (count($orders) > 0): ?>
        <?php foreach ($orders as $order): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Order ID:</strong> <?= htmlspecialchars($order['order_id']) ?> |
                    <strong>Date:</strong> <?= htmlspecialchars($order['created_at']) ?> |
                    <strong>Status:</strong> <?= htmlspecialchars($order['order_status']) ?>
                </div>
                <div class="card-body">
                    <p><strong>Payment Mode:</strong> <?= htmlspecialchars($order['payment_mode']) ?></p>
                    <p><strong>Total Price:</strong> ₱<?= number_format($order['total_price'], 2) ?></p>

                    <?php
                    // Fetch items for this order
                    $item_sql = "SELECT oi.*, p.product_name, p.product_img
                                 FROM order_items_tbl oi
                                 JOIN product_tbl p ON oi.product_id = p.product_id
                                 WHERE oi.order_id = :order_id";
                    $item_stmt = $conn->prepare($item_sql);
                    $item_stmt->bindParam(':order_id', $order['order_id'], PDO::PARAM_INT);
                    $item_stmt->execute();
                    $items = $item_stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>

                    <?php if (count($items) > 0): ?>
                        <table class="table table-bordered mt-3">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Image</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Customization</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                                        <td><img src="uploads/<?= htmlspecialchars($item['product_img']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" width="50"></td>
                                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                                        <td>₱<?= number_format($item['price'], 2) ?></td>
                                        <td><?= nl2br(htmlspecialchars($item['customization'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No items found for this order.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>You have not placed any orders yet.</p>
    <?php endif; ?>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="javascript/navbar-icons.js"></script>
</body>
</html>
