<?php
session_start();
require_once '../database/db.php';

if (!isset($_SESSION['user']['user_id']) || $_SESSION['user']['user_type'] !== 'admin') {
    header("Location: ../signup-page.php");
    exit;
}

$order_status_filter = $_GET['order_status'] ?? 'all';
$payment_status_filter = $_GET['payment_status'] ?? 'all';
$sort_order = $_GET['sort_order'] ?? 'desc';

$whereClauses = [];
$params = [];

if ($order_status_filter !== 'all') {
    $whereClauses[] = "order_status = :order_status";
    $params[':order_status'] = $order_status_filter;
}

if ($payment_status_filter !== 'all') {
    $whereClauses[] = "payment_status = :payment_status";
    $params[':payment_status'] = $payment_status_filter;
}

$whereSql = "";
if (count($whereClauses) > 0) {
    $whereSql = "WHERE " . implode(" AND ", $whereClauses);
}

$orderBy = ($sort_order === 'asc') ? 'ASC' : 'DESC';

$sql = "SELECT * FROM orders_tbl $whereSql ORDER BY 
    CASE WHEN order_status IN ('Delivered', 'Cancelled') THEN 1 ELSE 0 END,
    created_at $orderBy";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

    // Refresh the page to show updated data with current filters
    $redirectUrl = "admin-orders-page.php?order_status=" . urlencode($order_status_filter) . "&payment_status=" . urlencode($payment_status_filter) . "&sort_order=" . urlencode($sort_order);
    header("Location: $redirectUrl");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Adorable Knots</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Afacad:ital,wght@0,400..700;1,400..700&family=Caveat:wght@400..700&family=Dosis:wght@200..800&display=swap" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="css/navbar.css" />
    <link rel="stylesheet" href="../css/admin-orders-page.css" />
</head>
<body>
    <nav class="custom-navbar">
        <div class="custom-navbar-header">Admin Page</div>
        <div class="custom-navbar-contents">
            <div class="logo">
                <img src="../assets/web_img/ak-logo.png?v2" alt="Adorable Knots Logo" />
            </div>

            <div class="nav-links">
                <button><a href="admin-page.php">Products</a></button>
                <button class="active"><a href="admin-orders-page.php">Orders</a></button>
                <button><a href="analytics-page.php">Analytics</a></button>
            </div>

            <div class="logout">
                <a href="../login/logout.php">LOGOUT</a>
            </div>
        </div>
    </nav>

    <div class="content-scroll">
        <div class="container my-5">
            <div class="orders-header mb-4">All Orders</div>

            <div class="filter-status-container mb-4">
                <form id="filterForm" method="GET" class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label for="order_status" class="col-form-label">Order Status:</label>
                        <select name="order_status" id="order_status" class="form-select">
                            <option value="all" <?= $order_status_filter === 'all' ? 'selected' : '' ?>>All</option>
                            <option value="Pending" <?= $order_status_filter === 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="Processing" <?= $order_status_filter === 'Processing' ? 'selected' : '' ?>>Processing</option>
                            <option value="Shipped" <?= $order_status_filter === 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                            <option value="Delivered" <?= $order_status_filter === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                            <option value="Cancelled" <?= $order_status_filter === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>

                    <div class="col-auto">
                        <label for="payment_status" class="col-form-label">Payment Status:</label>
                        <select name="payment_status" id="payment_status" class="form-select">
                            <option value="all" <?= $payment_status_filter === 'all' ? 'selected' : '' ?>>All</option>
                            <option value="Pending" <?= $payment_status_filter === 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="Paid" <?= $payment_status_filter === 'Paid' ? 'selected' : '' ?>>Paid</option>
                            <option value="Failed" <?= $payment_status_filter === 'Failed' ? 'selected' : '' ?>>Failed</option>
                            <option value="Refunded" <?= $payment_status_filter === 'Refunded' ? 'selected' : '' ?>>Refunded</option>
                        </select>
                    </div>

                    <div class="col-auto">
                        <label for="sort_order" class="col-form-label">Sort By Date:</label>
                        <select name="sort_order" id="sort_order" class="form-select">
                            <option value="desc" <?= $sort_order === 'desc' ? 'selected' : '' ?>>Latest to Oldest</option>
                            <option value="asc" <?= $sort_order === 'asc' ? 'selected' : '' ?>>Oldest to Latest</option>
                        </select>
                    </div>
                </form>
            </div>

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
                                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>" />

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
                            <?php
                            // Fetch user info for this order
                            $user_stmt = $conn->prepare("SELECT fullname, username FROM users_tbl WHERE user_id = :user_id");
                            $user_stmt->bindParam(':user_id', $order['user_id'], PDO::PARAM_STR);
                            $user_stmt->execute();
                            $user_info = $user_stmt->fetch(PDO::FETCH_ASSOC);
                            ?>

                            <?php if ($user_info): ?>
                                <p><strong>Customer Name:</strong> <?= htmlspecialchars($user_info['fullname']) ?></p>
                                <p><strong>Username:</strong> <?= htmlspecialchars($user_info['username']) ?></p>
                            <?php else: ?>
                                <p><strong>Customer:</strong> Unknown</p>
                            <?php endif; ?>

                            <p><strong>Payment Mode:</strong> <?= htmlspecialchars($order['payment_mode']) ?></p>
                            <p><strong>Total Price:</strong> ₱<?= number_format($order['total_price'], 2) ?></p>

                            <?php
                            $address_stmt = $conn->prepare("SELECT * FROM address_tbl WHERE address_id = :address_id");
                            $address_stmt->bindParam(':address_id', $order['address_id'], PDO::PARAM_INT);
                            $address_stmt->execute();
                            $address = $address_stmt->fetch(PDO::FETCH_ASSOC);
                            ?>

                            <?php if ($address): ?>
                                <p><strong>Shipping Address:</strong><br>
                                    <?= htmlspecialchars($address['street_details']) ?>,
                                    <?= htmlspecialchars($address['barangay']) ?>,
                                    <?= htmlspecialchars($address['city']) ?>,
                                    <?= htmlspecialchars($address['province']) ?>,
                                    <?= htmlspecialchars($address['postal_code']) ?>
                                </p>
                            <?php else: ?>
                                <p><strong>Shipping Address:</strong> Not available.</p>
                            <?php endif; ?>

                            <?php
                           $item_sql = "SELECT oi.*, p.product_name, p.product_img, p.shipping_fee
                                        FROM order_items_tbl oi
                                        LEFT JOIN product_tbl p ON oi.product_id = p.product_id
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
                                            <th>Price</th>
                                            <th>Customization</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($item['product_name'] ?? 'Unknown product') ?></td>
                                                <td>
                                                    <?php if (!empty($item['product_img'])): ?>
                                                        <img src="../uploads/<?= htmlspecialchars($item['product_img']) ?>" alt="Product Image" style="max-width: 80px;" />
                                                    <?php else: ?>
                                                        No image
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= $item['quantity'] ?></td>
                                                <td>
                                                    ₱<?= number_format($item['price'], 2) ?>
                                                    <?php if (isset($item['shipping_fee'])): ?>
                                                        + ₱<?= number_format($item['shipping_fee'], 2) ?> Shipping fee
                                                    <?php endif; ?>
                                                </td>

                                                <td>
                                                    <?php
                                                    $customization = $item['customization'];
                                                    if (!empty($customization)) {
                                                        echo htmlspecialchars($customization);
                                                    } else {
                                                        echo 'None';
                                                    }
                                                    ?>
                                                </td>
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
                <p>No orders found.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.querySelectorAll('#order_status, #payment_status, #sort_order').forEach(select => {
            select.addEventListener('change', () => {
                document.getElementById('filterForm').submit();
            });
        });
    </script>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
