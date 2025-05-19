<?php
session_start();
require_once 'database/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user']['user_id'])) {
    header("Location: signup-page.php");
    exit;
}

$user_id = $_SESSION['user']['user_id'];

$sql = "SELECT * FROM orders_tbl WHERE user_id = :user_id ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
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
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Afacad:ital,wght@0,400..700;1,400..700&family=Caveat:wght@400..700&family=Dosis:wght@200..800&display=swap" rel="stylesheet">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/order-page.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="custom-navbar">
        <div class="logo">
            <img src="assets/web_img/ak-logo.png?v2" alt="Adorable Knots Logo">
        </div>
        <div class="nav-links">
            <button><img src="assets/icons/home.png"><a href="home.php">Home</a></button>
            <button><img src="assets/icons/bag.png"><a href="store-page.php">Shop Now</a></button>
            <button class="active"><img src="assets/icons/order.png"><a href="<?= isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'orders-page.php' : 'signup-page.php' ?>">My Orders</a></button>
            <button><img src="assets/icons/user.png"><a href="<?= isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'account-page.php' : 'signup-page.php' ?>">Account</a></button>
            <button><img src="assets/icons/cart.png"><a href="<?= isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'cart-page.php' : 'signup-page.php' ?>">Cart</a></button>
        </div>
    </nav>

    <div class="content-scroll">
        <div class="container my-5">
            <div class="orders-header mb-4">My Orders</div>

            <?php if (count($orders) > 0): ?>
                <?php foreach ($orders as $order): ?>

                    <?php
                    // Skip orders with status delivered or cancelled
                    if (in_array(strtolower($order['order_status']), ['delivered', 'cancelled'])) {
                        continue;
                    }
                    ?>

                    <div class="card mb-4" id="order-card-<?= htmlspecialchars($order['order_id']) ?>">
                        <div class="card-header">
                            <strong>Placed Date:</strong> <?= htmlspecialchars($order['created_at']) ?> |
                            <strong>Order Status:</strong> <span id="status-<?= htmlspecialchars($order['order_id']) ?>"><?= htmlspecialchars($order['order_status']) ?></span> |
                            <strong>Payment Status:</strong> <?= htmlspecialchars($order['payment_status']) ?>
                        </div>

                        <div class="card-body">
                            <p><strong>Payment Mode:</strong> <?= htmlspecialchars($order['payment_mode']) ?></p>
                            <p><strong>Total Price:</strong> ₱<?= number_format($order['total_price'], 2) ?></p>

                            <?php
                            $item_sql = "SELECT oi.*, p.product_name, p.product_img, p.shipping_fee
                                         FROM order_items_tbl oi
                                         JOIN product_tbl p ON oi.product_id = p.product_id
                                         WHERE oi.order_id = :order_id";
                            $item_stmt = $conn->prepare($item_sql);
                            $item_stmt->bindParam(':order_id', $order['order_id'], PDO::PARAM_STR);
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
                                            <th>Price</th> <!-- display price + shipping fee as text -->
                                            <th>Customization</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($item['product_name']) ?></td>
                                                <td><img src="uploads/<?= htmlspecialchars($item['product_img']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" width="50"></td>
                                                <td><?= htmlspecialchars($item['quantity']) ?></td>
                                                <td>
                                                    ₱<?= number_format($item['price'], 2) ?> + ₱<?= number_format($item['shipping_fee'], 2) ?> Shipping fee
                                                </td>
                                                <td><?= nl2br(htmlspecialchars($item['customization'])) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p>No items found for this order.</p>
                            <?php endif; ?>

                            <?php
                            // Show cancel button only if status is pending or processing
                            if (in_array(strtolower($order['order_status']), ['pending', 'processing'])): ?>
                                <div class="d-flex justify-content-end mt-3">
                                    <button class="btn btn-secondary cancel-btn" data-order-id="<?= htmlspecialchars($order['order_id']) ?>" data-status="<?= strtolower($order['order_status']) ?>">Cancel Order</button>
                                </div>
                            <?php else: ?>
                                <div class="d-flex justify-content-end mt-3">
                                    <button class="btn btn-secondary cancel-btn" data-order-id="<?= htmlspecialchars($order['order_id']) ?>" data-status="<?= strtolower($order['order_status']) ?>">Cancel Order</button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-orders">You have not placed any orders yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="javascript/navbar-icons.js"></script>
    <script>
    document.querySelectorAll('.cancel-btn').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const status = this.getAttribute('data-status');

            if (status === 'pending' || status === 'processing') {
                if (confirm('Are you sure you want to cancel this order?')) {
                    // Send AJAX request to cancel the order
                    fetch('orders/cancel-order.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `order_id=${encodeURIComponent(orderId)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Order cancelled successfully.');
                            // Optionally update the UI, e.g., hide or update the order card
                            document.getElementById('order-card-' + orderId).remove();
                        } else {
                            alert('Failed to cancel the order: ' + data.message);
                        }
                    })
                    .catch(() => {
                        alert('An error occurred while cancelling the order.');
                    });
                }
            } else {
                alert('Order cancellation is not allowed. Current status: ' + status.charAt(0).toUpperCase() + status.slice(1));
            }
        });
    });
    </script>
</body>
</html>
