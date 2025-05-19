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

$stmt = $conn->prepare("SELECT COUNT(*) FROM address_tbl WHERE address_id = :address_id");
$stmt->bindParam(':address_id', $address_id);
$stmt->execute();
if ($stmt->fetchColumn() == 0) {
    echo "The selected address does not exist.";
    exit;
}

if (empty($_POST['products']) || !is_array($_POST['products'])) {
    echo "No products selected for checkout.";
    exit;
}

$products = $_POST['products'];
$cart_ids = [];

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
    
    $order_status = 'Pending';

    // Calculate total price including shipping fee
    $total_price = 0;
    foreach ($products as $prod) {
        $product_id = $prod['product_id'] ?? null;
        $quantity = isset($prod['quantity']) && is_numeric($prod['quantity']) && $prod['quantity'] > 0 ? (int)$prod['quantity'] : 1;
        if (!$product_id) continue;

        $stmtPrice = $conn->prepare("SELECT product_price, shipping_fee FROM product_tbl WHERE product_id = ?");
        $stmtPrice->execute([$product_id]);
        $row = $stmtPrice->fetch(PDO::FETCH_ASSOC);

        $price = $row['product_price'] ?? 0;
        $shipping_fee = $row['shipping_fee'] ?? 0;

        $total_price += ($price + $shipping_fee) * $quantity;
    }

    // Insert order
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

    // Prepare statements for inserting order items and updating stock
    $stmtInsertItem = $conn->prepare("INSERT INTO order_items_tbl 
        (item_id, order_id, product_id, quantity, price, customization) 
        VALUES (:item_id, :order_id, :product_id, :quantity, :price, :customization)");

    $stock_check_stmt = $conn->prepare("SELECT product_stock FROM product_tbl WHERE product_id = ?");
    $update_stock_stmt = $conn->prepare("UPDATE product_tbl SET product_stock = product_stock - :quantity WHERE product_id = :product_id");

    foreach ($products as $prod) {
        $product_id = $prod['product_id'] ?? null;
        $quantity = isset($prod['quantity']) && is_numeric($prod['quantity']) && $prod['quantity'] > 0 ? (int)$prod['quantity'] : 1;
        if (!$product_id) continue;

        // Generate unique item_id
        do {
            $item_id = 'IT' . substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6);
            $stmtCheckItem = $conn->prepare("SELECT COUNT(*) FROM order_items_tbl WHERE item_id = ?");
            $stmtCheckItem->execute([$item_id]);
            $itemExists = $stmtCheckItem->fetchColumn();
        } while ($itemExists);

        // Get price per product (without shipping fee)
        $stmtPrice = $conn->prepare("SELECT product_price FROM product_tbl WHERE product_id = ?");
        $stmtPrice->execute([$product_id]);
        $price = $stmtPrice->fetchColumn();
        if ($price === false) $price = 0;

        $stmtInsertItem->execute([
            ':item_id' => $item_id,
            ':order_id' => $order_id,
            ':product_id' => $product_id,
            ':quantity' => $quantity,
            ':price' => $price,
            ':customization' => $customization
        ]);

        // Update product stock
        $stock_check_stmt->execute([$product_id]);
        $stock = $stock_check_stmt->fetchColumn();

        if ($stock > 0) {
            $new_stock = $stock - $quantity;
            if ($new_stock >= 0) {
                $update_stock_stmt->execute([
                    ':quantity' => $quantity,
                    ':product_id' => $product_id
                ]);
            }
        }
    }

    // Delete checked out items from cart
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
?>
