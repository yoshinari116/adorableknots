<?php
session_start();
require_once '../database/db.php';

if (!isset($_SESSION['user']['user_id'])) {
    echo "User not logged in.";
    exit;
}

$user_id = $_SESSION['user']['user_id'];
$address_id = $_POST['address_id'] ?? null;
$payment_mode = $_POST['payment_mode'] ?? null;
$customization = $_POST['customization'] ?? '';

if (!$address_id || !$payment_mode) {
    echo "Required fields missing!";
    exit;
}

// Validate address existence
$stmt = $conn->prepare("SELECT COUNT(*) FROM address_tbl WHERE address_id = :address_id");
$stmt->bindParam(':address_id', $address_id);
$stmt->execute();
if ($stmt->fetchColumn() == 0) {
    echo "The selected address does not exist.";
    exit;
}

// Validate products data
if (empty($_POST['products']) || !is_array($_POST['products'])) {
    echo "No products selected for checkout.";
    exit;
}

$products = $_POST['products'];
$cart_ids = [];

// Collect cart IDs to delete later
foreach ($products as $prod) {
    if (!empty($prod['cart_id'])) {
        $cart_ids[] = $prod['cart_id'];
    }
}

$conn->beginTransaction();

try {
    // Generate unique order_id
    do {
        $order_id = '0' . substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 7);
        $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM orders_tbl WHERE order_id = ?");
        $stmtCheck->execute([$order_id]);
        $exists = $stmtCheck->fetchColumn();
    } while ($exists);

    // Get order_status from first product or default to 'Pending'
    $first_product_id = array_key_first($products);
    $stmt = $conn->prepare("SELECT product_status FROM product_tbl WHERE product_id = ?");
    $stmt->execute([$first_product_id]);
    $order_status = $stmt->fetchColumn() ?: 'Pending';

    // Calculate total price BEFORE inserting order
    $total_price = 0;
    foreach ($products as $prod) {
        $product_id = $prod['product_id'] ?? null;
        $quantity = isset($prod['quantity']) && is_numeric($prod['quantity']) && $prod['quantity'] > 0 ? (int)$prod['quantity'] : 1;
        if (!$product_id) continue;

        $stmtPrice = $conn->prepare("SELECT product_price FROM product_tbl WHERE product_id = ?");
        $stmtPrice->execute([$product_id]);
        $price = $stmtPrice->fetchColumn();
        if ($price === false) $price = 0;

        $total_price += $price * $quantity;
    }

    // Insert order including total_price
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

    // Prepare order items insertion
    $stmtInsertItem = $conn->prepare("INSERT INTO order_items_tbl 
        (item_id, order_id, product_id, quantity, price, customization) 
        VALUES (:item_id, :order_id, :product_id, :quantity, :price, :customization)");

    foreach ($products as $prod) {
        $product_id = $prod['product_id'] ?? null;
        $quantity = isset($prod['quantity']) && is_numeric($prod['quantity']) && $prod['quantity'] > 0 ? (int)$prod['quantity'] : 1;

        if (!$product_id) continue;

        // Generate unique item_id for this order item
        do {
            $item_id = 'IT' . substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6);
            $stmtCheckItem = $conn->prepare("SELECT COUNT(*) FROM order_items_tbl WHERE item_id = ?");
            $stmtCheckItem->execute([$item_id]);
            $itemExists = $stmtCheckItem->fetchColumn();
        } while ($itemExists);

        // Get product price from product_tbl
        $stmtPrice = $conn->prepare("SELECT product_price FROM product_tbl WHERE product_id = ?");
        $stmtPrice->execute([$product_id]);
        $price = $stmtPrice->fetchColumn();
        if ($price === false) {
            $price = 0; // default to 0 if price not found
        }

        // Insert order item
        $stmtInsertItem->execute([
            ':item_id' => $item_id,
            ':order_id' => $order_id,
            ':product_id' => $product_id,
            ':quantity' => $quantity,
            ':price' => $price,
            ':customization' => $customization
        ]);
    }

    // Delete purchased items from cart_tbl
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
    echo "Error: " . $e->getMessage();
}
