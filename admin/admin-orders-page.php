<?php
session_start();
require_once '../database/db.php';

// Optional: redirect if not admin
if (!isset($_SESSION['user']['user_id']) || $_SESSION['user']['user_type'] !== 'admin') {
    header("Location: ../signup-page.php");
    exit;
}

// Handle form submission
$updateSuccess = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];
    $payment_status = $_POST['payment_status'];

    $update_sql = "UPDATE orders_tbl SET order_status = :order_status, payment_status = :payment_status WHERE order_id = :order_id";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->execute([
        ':order_status' => $order_status,
        ':payment_status' => $payment_status,
        ':order_id' => $order_id
    ]);

    $updateSuccess = true;
}

// Fetch all orders
$sql = "SELECT * FROM orders_tbl ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    <!-- Styles -->
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="../css/admin-orders-page.css">
</head>
<body>

    <nav class="custom-navbar">
        <div class="custom-navbar-header">Admin Page</div>
        <div class="custom-navbar-contents">
            <div class="logo">
                <img src="../assets/web_img/ak-logo.png?v2" alt="Adorable Knots Logo">
            </div>

            <div class="nav-links">
                <button><a href="admin-page.php">Products</a></button>
                <button class="active"><a href="admin-orders-page.php">Orders</a></button>
                <button><a href="#">Analytics</a></button>
            </div>

            <div class="logout">
                <a href="../login/logout.php">LOGOUT</a>
            </div>
        </div>
    </nav>

    <div class="content-scroll">
        <div class="container my-5">
            <div class="orders-header mb-4">All Orders</div>

            <?php if ($updateSuccess): ?>
                <div class="alert alert-success">Order status updated successfully.</div>
            <?php endif; ?>

            <?php if (count($orders) > 0): ?>
                <?php foreach ($orders as $order): ?>
                    <form method="POST" class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <strong>Order ID:</strong> <?= htmlspecialchars($order['order_id']) ?> |
                                <strong>Date:</strong> <?= htmlspecialchars($order['created_at']) ?>
                            </div>
                            <div class="d-flex gap-2">
                                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">

                                <select name="order_status" class="form-select">
                                    <option value="Pending" <?= $order['order_status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="Processing" <?= $order['order_status'] === 'Processing' ? 'selected' : '' ?>>Processing</option>
                                    <option value="Shipped" <?= $order['order_status'] === 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                                    <option value="Delivered" <?= $order['order_status'] === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                                    <option value="Cancelled" <?= $order['order_status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>

                                <select name="payment_status" class="form-select">
                                    <option value="Pending" <?= $order['payment_status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="Paid" <?= $order['payment_status'] === 'Paid' ? 'selected' : '' ?>>Paid</option>
                                    <option value="Failed" <?= $order['payment_status'] === 'Failed' ? 'selected' : '' ?>>Failed</option>
                                    <option value="Refunded" <?= $order['payment_status'] === 'Refunded' ? 'selected' : '' ?>>Refunded</option>
                                </select>

                                <button type="submit" class="btn btn-success">Save</button>
                            </div>
                        </div>

                        <div class="card-body">
                            <p><strong>Payment Mode:</strong> <?= htmlspecialchars($order['payment_mode']) ?></p>
                            <p><strong>Total Price:</strong> ₱<?= number_format($order['total_price'], 2) ?></p>

                            <?php
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
                                                <td><img src="../uploads/<?= htmlspecialchars($item['product_img']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" width="50"></td>
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
                    </form>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-orders">No orders found.</p>
            <?php endif; ?>
        </div>
    </div> 
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../javascript/admin-modals.js"></script>
    <script src="../javascript/admin-confirm.js"></script>
</body>
</html>