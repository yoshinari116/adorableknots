<?php
    session_start();
    require_once 'database/db.php';

    if (!isset($_SESSION['user'])) {
        header('Location: signup-page.php');
        exit;
    }

    $userId = $_SESSION['user']['user_id'];

    // Remove from cart
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_cart_id'])) {
        $removeStmt = $conn->prepare("DELETE FROM cart_tbl WHERE cart_id = ? AND user_id = ?");
        $removeStmt->execute([$_POST['remove_cart_id'], $userId]);
        header("Location: cart-page.php");
        exit;
    }

    // Add to cart (in case it's reused from store)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
        $productId = $_POST['product_id'];
        $cart_id = 'CRT' . date('ymd') . date('s') . mt_rand(1000, 9999);

        $insertStmt = $conn->prepare("INSERT INTO cart_tbl (cart_id, user_id, product_id) VALUES (?, ?, ?)");
        $insertStmt->execute([$cart_id, $userId, $productId]);

        header("Location: cart-page.php");
        exit;
    }

    // Fetch cart items
    $stmt = $conn->prepare("
        SELECT p.*, c.cart_id, c.added_at
        FROM cart_tbl c
        JOIN product_tbl p ON c.product_id = p.product_id
        WHERE c.user_id = ?
    ");
    $stmt->execute([$userId]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cart</title>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Afacad:ital,wght@0,400..700;1,400..700&family=Caveat:wght@400..700&family=Dosis:wght@200..800&display=swap" rel="stylesheet">

        <!-- Custom Stylesheets -->
        <link rel="stylesheet" href="css/home.css">
        <link rel="stylesheet" href="css/cart-page.css">
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
                <button><img src="assets/icons/order.png"><a href="<?= isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'orders-page.php' : 'signup-page.php' ?>">My Orders</a></button>
                <button><img src="assets/icons/user.png"><a href="<?= isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'account-page.php' : 'signup-page.php' ?>">Account</a></button>
                <button class="active"><img src="assets/icons/cart.png"><a href="<?= isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'cart-page.php' : 'signup-page.php' ?>">Cart</a></button>
            </div>
        </nav>

        <div class="content-scroll">
            <div class="container my-5">
                <div class="orders-header mb-4">My Cart</div>

                <?php if (count($cartItems) > 0): ?>
                    <?php foreach ($cartItems as $item): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <strong>Added At:</strong> <?= htmlspecialchars($item['added_at']) ?>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Image</th>
                                            <th>Description</th>
                                            <th>Price</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                                            <td><img src="uploads/<?= htmlspecialchars($item['product_img']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" width="50"></td>
                                            <td><?= nl2br(htmlspecialchars($item['product_description'])) ?></td>
                                            <td>₱<?= number_format($item['product_price'], 2) ?></td>
                                            <td><?= htmlspecialchars($item['product_status']) ?></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <form method="post" class="me-2">
                                    <input type="hidden" name="remove_cart_id" value="<?= htmlspecialchars($item['cart_id']) ?>">
                                    <button type="submit" class="btn btn-secondary" style="border: none;">Remove</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <form action="checkout-page.php?from=cart" method="post" class="d-flex justify-content-end mt-3">

                        <?php foreach ($cartItems as $item): ?>
                            <input type="hidden" name="product_ids[]" value="<?= htmlspecialchars($item['product_id']) ?>">
                        <?php endforeach; ?>
                        <button type="submit" class="btn btn-success" style="border: none;">Proceed to Checkout</button>
                    </form>

                <?php else: ?>
                    <p class="no-cart">Your cart is empty.</p>
                <?php endif; ?>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="javascript/navbar-icons.js"></script>
    </body>
    </html>
