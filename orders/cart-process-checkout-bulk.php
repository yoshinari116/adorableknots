<?php
session_start();
require_once '../database/db.php';

if (!isset($_SESSION['user']['user_id'])) {
    exit;
}

$user_id = $_SESSION['user']['user_id'];
$address_id = $_POST['address_id'] ?? null;
$payment_mode = $_POST['payment_mode'] ?? null;
$products = $_POST['products'] ?? [];

if (!$address_id || !$payment_mode || empty($products)) {
    exit;
}

// Validate address existence
$stmt = $conn->prepare("SELECT COUNT(*) FROM address_tbl WHERE address_id = :address_id");
$stmt->bindParam(':address_id', $address_id);
$stmt->execute();
if ($stmt->fetchColumn() == 0) {
    exit;
}

$cart_ids = [];
$conn->beginTransaction();

try {
    // Generate unique order_id
    do {
        $order_id = '0' . substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 7);
        $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM orders_tbl WHERE order_id = ?");
        $stmtCheck->execute([$order_id]);
        $exists = $stmtCheck->fetchColumn();
    } while ($exists);

    $order_status = 'Pending';
    $total_price = 0;

    // Calculate total price (including shipping_fee)
    $stmtPrice = $conn->prepare("SELECT product_price, shipping_fee FROM product_tbl WHERE product_id = ?");

    foreach ($products as $prod) {
        $product_id = $prod['product_id'] ?? null;
        $quantity = isset($prod['quantity']) && $prod['quantity'] > 0 ? (int)$prod['quantity'] : 1;

        if (!$product_id) continue;

        $stmtPrice->execute([$product_id]);
        $row = $stmtPrice->fetch(PDO::FETCH_ASSOC);

        $product_price = isset($row['product_price']) ? floatval($row['product_price']) : 0;
        $shipping_fee = isset($row['shipping_fee']) ? floatval($row['shipping_fee']) : 0;

        $total_price += ($product_price * $quantity) + $shipping_fee;
    }

    // Insert into orders_tbl
    $stmt = $conn->prepare("INSERT INTO orders_tbl 
        (order_id, user_id, address_id, payment_mode, order_status, payment_status, total_price, created_at)
        VALUES (:order_id, :user_id, :address_id, :payment_mode, :order_status, 'Pending', :total_price, NOW())");

    $stmt->execute([
        ':order_id' => $order_id,
        ':user_id' => $user_id,
        ':address_id' => $address_id,
        ':payment_mode' => $payment_mode,
        ':order_status' => $order_status,
        ':total_price' => $total_price
    ]);

    $stmtInsertDetail = $conn->prepare("INSERT INTO order_items_tbl 
        (order_id, product_id, quantity, customization)
        VALUES (:order_id, :product_id, :quantity, :customization)");

    // Prepare stock update
    $stmtStock = $conn->prepare("SELECT product_stock FROM product_tbl WHERE product_id = ?");
    $stmtUpdateStock = $conn->prepare("UPDATE product_tbl SET product_stock = product_stock - :qty WHERE product_id = :product_id");

    foreach ($products as $prod) {
        $product_id = $prod['product_id'] ?? null;
        $quantity = isset($prod['quantity']) && $prod['quantity'] > 0 ? (int)$prod['quantity'] : 1;
        $customization = $prod['customization'] ?? '';
        $cart_id = $prod['cart_id'] ?? null;

        if (!$product_id) continue;

        $stmtInsertDetail->execute([
            ':order_id' => $order_id,
            ':product_id' => $product_id,
            ':quantity' => $quantity,
            ':customization' => $customization
        ]);

        // Update stock only if current stock > 0
        $stmtStock->execute([$product_id]);
        $current_stock = $stmtStock->fetchColumn();

        if ($current_stock > 0 && $current_stock >= $quantity) {
            $stmtUpdateStock->execute([
                ':qty' => $quantity,
                ':product_id' => $product_id
            ]);
        }

        if ($cart_id) {
            $cart_ids[] = $cart_id;
        }
    }

    // Delete purchased cart items
    if (!empty($cart_ids)) {
        $inQuery = implode(',', array_fill(0, count($cart_ids), '?'));
        $stmtDeleteCart = $conn->prepare("DELETE FROM cart_tbl WHERE cart_id IN ($inQuery) AND user_id = ?");
        $stmtDeleteCart->execute([...$cart_ids, $user_id]);
    }

    $conn->commit();
    header("Location: ../orders-page.php");
    exit;

} catch (Exception $e) {
    $conn->rollBack();
    exit;
}
