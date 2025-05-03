<?php
session_start();
require_once 'database/db.php';

// Add to cart logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove_id'])) {
        $removeId = $_POST['remove_id'];
        unset($_SESSION['cart'][$removeId]);
        header("Location: cart-page.php");
        exit;
    }

    if (isset($_POST['checkout'])) {
        header("Location: checkout-page.php");
        exit;
    }

    $productId = $_POST['product_id'] ?? null;
    $quantity = (int) ($_POST['quantity'] ?? 1);

    if ($productId) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }

        header("Location: cart-page.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 20px;
        }

        .cart-container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .back-home {
            display: inline-block;
            margin-bottom: 20px;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            transition: background 0.3s ease;
        }

        .back-home:hover {
            background-color: #0056b3;
        }

        .cart-item {
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .item-info {
            flex: 1;
        }

        .item-name {
            font-size: 18px;
            font-weight: bold;
            color: #444;
        }

        .item-quantity,
        .item-price,
        .item-subtotal {
            font-size: 14px;
            color: #666;
        }

        .remove-button {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 6px 10px;
            border-radius: 6px;
            cursor: pointer;
        }

        .remove-button:hover {
            background-color: #e60000;
        }

        .checkout-btn {
            display: block;
            width: 100%;
            background-color: #28a745;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            margin-top: 20px;
            cursor: pointer;
        }

        .checkout-btn:hover {
            background-color: #218838;
        }

        .empty-cart {
            text-align: center;
            padding: 50px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="cart-container">
        <h1>Your Shopping Cart</h1>

        <a href="store-page.php" class="back-home">← Back to Home</a>

        <?php if (empty($_SESSION['cart'])): ?>
            <div class="empty-cart">Your cart is empty.</div>
        <?php else: ?>
            <form method="post">
                <?php
                $total = 0;
                foreach ($_SESSION['cart'] as $id => $qty):
                    $stmt = $conn->prepare("SELECT product_name, product_price FROM product_tbl WHERE product_id = ?");
                    $stmt->execute([$id]);
                    $product = $stmt->fetch();

                    if ($product):
                        $name = htmlspecialchars($product['product_name']);
                        $price = $product['product_price'];
                        $subtotal = $qty * $price;
                        $total += $subtotal;
                ?>
                    <div class="cart-item">
                        <div class="item-info">
                            <div class="item-name"><?= $name ?></div>
                            <div class="item-quantity">Quantity: <?= $qty ?></div>
                            <div class="item-price">Price: ₱<?= number_format($price, 2) ?></div>
                            <div class="item-subtotal">Subtotal: ₱<?= number_format($subtotal, 2) ?></div>
                        </div>
                        <div>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="remove_id" value="<?= $id ?>">
                                <button type="submit" class="remove-button">Remove</button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="cart-item">
                        <div class="item-info">Unknown Product (ID: <?= $id ?>) — Quantity: <?= $qty ?></div>
                    </div>
                <?php endif; endforeach; ?>
            </form>

            <div style="text-align:right; margin-top:20px; font-size:18px; font-weight:bold;">
                Total: ₱<?= number_format($total, 2) ?>
            </div>

            <form method="post" action="checkout-page.php?from=cart">
                <button type="submit" name="checkout" class="checkout-btn">Proceed to Checkout</button>
            </form>

        <?php endif; ?>
    </div>
</body>
</html>
