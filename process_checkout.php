<?php
session_start();
require_once 'database/db.php';

if (!isset($_SESSION['user']['user_id'])) {
    header("Location: signup-page.php");
    exit;
}

$user_id = $_SESSION['user']['user_id'];

// Validate common fields
if (!isset($_POST['address_id'], $_POST['payment_mode']) || !is_numeric($_POST['address_id'])) {
    echo "Invalid form submission.";
    exit;
}

$address_id = intval($_POST['address_id']);
$payment_mode = $_POST['payment_mode'];
$customization = $_POST['customization'] ?? '';
$from_cart = isset($_POST['from_cart']); // Will be true if it's a cart checkout

try {
    $conn->beginTransaction();

    // Calculate total price
    $total_price = 0;
    $items = [];

    if ($from_cart && isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $stmt = $conn->prepare("SELECT product_price FROM product_tbl WHERE product_id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                $price = $product['product_price'];
                $total_price += $price * $quantity;
                $items[] = [
                    'product_id' => $product_id,
                    'quantity' => $quantity,
                    'price' => $price
                ];
            }
        }
    } else {
        // Single product checkout
        if (!isset($_POST['product_id'], $_POST['product_price']) || !is_numeric($_POST['product_id']) || !is_numeric($_POST['product_price'])) {
            echo "Invalid form submission.";
            exit;
        }

        $product_id = intval($_POST['product_id']);
        $product_price = floatval($_POST['product_price']);
        $quantity = isset($_POST['quantity']) && is_numeric($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        $total_price = $product_price * $quantity;

        $items[] = [
            'product_id' => $product_id,
            'quantity' => $quantity,
            'price' => $product_price
        ];
    }

    // Insert into orders_tbl
    $order_sql = "INSERT INTO orders_tbl (user_id, address_id, payment_mode, total_price, order_status, created_at)
                  VALUES (:user_id, :address_id, :payment_mode, :total_price, 'Pending', NOW())";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->execute([
        ':user_id' => $user_id,
        ':address_id' => $address_id,
        ':payment_mode' => $payment_mode,
        ':total_price' => $total_price
    ]);

    $order_id = $conn->lastInsertId();

    // Insert each item into order_items_tbl
    $item_sql = "INSERT INTO order_items_tbl (order_id, product_id, quantity, price, customization)
                 VALUES (:order_id, :product_id, :quantity, :price, :customization)";
    $item_stmt = $conn->prepare($item_sql);

    foreach ($items as $item) {
        $item_stmt->execute([
            ':order_id' => $order_id,
            ':product_id' => $item['product_id'],
            ':quantity' => $item['quantity'],
            ':price' => $item['price'],
            ':customization' => $customization
        ]);
    }

    $conn->commit();

    // Clear cart if from cart checkout
    if ($from_cart) {
        unset($_SESSION['cart']);
    }

    header("Location: orders-page.php");
    exit;

} catch (Exception $e) {
    $conn->rollBack();
    echo "Failed to process your order. Please try again later.";
    // Uncomment for debugging:
    // echo "Error: " . $e->getMessage();
    exit;
}
