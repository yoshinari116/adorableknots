<?php
session_start();
require_once '../database/db.php';

if (!isset($_SESSION['user']['user_id'])) {
    header("Location: signup-page.php");
    exit;
}

$user_id = $_SESSION['user']['user_id'];

if (!isset($_POST['address_id'], $_POST['payment_mode'])) {
    echo "Invalid form submission. Missing address_id or payment_mode.";
    exit;
}

$address_id = trim($_POST['address_id']);
if ($address_id === '') {
    echo "Please select a delivery address.";
    exit;
}

$payment_mode = $_POST['payment_mode'];
$valid_payment_modes = ['COD', 'GCash'];
if (!in_array($payment_mode, $valid_payment_modes)) {
    echo "Invalid payment method.";
    exit;
}

// Verify address belongs to user
$stmt = $conn->prepare("SELECT * FROM address_tbl WHERE address_id = ? AND user_id = ?");
$stmt->execute([$address_id, $user_id]);
if ($stmt->rowCount() === 0) {
    echo "Invalid or unauthorized address ID.";
    exit;
}

$items = [];

if (isset($_POST['products'][0]) && isset($_POST['product_id'])) {
    $product_data = $_POST['products'][0];
    $product_id = $_POST['product_id'];
    $product_price = isset($product_data['product_price']) ? floatval($product_data['product_price']) : 0;
    $quantity = isset($product_data['quantity']) ? intval($product_data['quantity']) : 1;
    $customization = substr(trim($product_data['customization'] ?? ''), 0, 255);

    // Fetch shipping_fee from product_tbl for this product
    $shipping_fee = 0;
    $stmt_fee = $conn->prepare("SELECT shipping_fee FROM product_tbl WHERE product_id = ?");
    $stmt_fee->execute([$product_id]);
    if ($row_fee = $stmt_fee->fetch(PDO::FETCH_ASSOC)) {
        $shipping_fee = floatval($row_fee['shipping_fee']);
    }

    if ($product_id && $quantity > 0) {
        $items[] = [
            'product_id' => $product_id,
            'quantity' => $quantity,
            'price' => $product_price,
            'shipping_fee' => $shipping_fee,
            'customization' => $customization
        ];
    }
}

if (empty($items)) {
    echo "No valid items to insert.";
    exit;
}

try {
    $conn->beginTransaction();

    $total_price = 0;
    foreach ($items as $item) {
        // Add product price * quantity plus shipping fee to total
        $total_price += ($item['price'] * $item['quantity']) + $item['shipping_fee'];
    }

    // Generate order ID
    $order_id = '0' . substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 7);

    // Insert order
    $order_sql = "INSERT INTO orders_tbl 
        (order_id, user_id, address_id, payment_mode, total_price, order_status, payment_status, created_at)
        VALUES (:order_id, :user_id, :address_id, :payment_mode, :total_price, 'Pending', 'Pending', NOW())";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->execute([
        ':order_id' => $order_id,
        ':user_id' => $user_id,
        ':address_id' => $address_id,
        ':payment_mode' => $payment_mode,
        ':total_price' => $total_price
    ]);

    // Prepare item insert and stock queries
    $item_sql = "INSERT INTO order_items_tbl 
        (item_id, order_id, product_id, quantity, price, customization)
        VALUES (:item_id, :order_id, :product_id, :quantity, :price, :customization)";
    $item_stmt = $conn->prepare($item_sql);

    $stock_check_stmt = $conn->prepare("SELECT product_stock FROM product_tbl WHERE product_id = ?");
    $update_stock_stmt = $conn->prepare("UPDATE product_tbl SET product_stock = product_stock - :quantity WHERE product_id = :product_id");

    foreach ($items as $item) {
        $item_id = 'IT' . substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6);
        $item_stmt->execute([
            ':item_id' => $item_id,
            ':order_id' => $order_id,
            ':product_id' => $item['product_id'],
            ':quantity' => $item['quantity'],
            ':price' => $item['price'],
            ':customization' => $item['customization']
        ]);

        $stock_check_stmt->execute([$item['product_id']]);
        $stock = $stock_check_stmt->fetchColumn();

        if ($stock > 0) {
            $update_stock_stmt->execute([
                ':quantity' => $item['quantity'],
                ':product_id' => $item['product_id']
            ]);
        }
    }

    $conn->commit();

    header("Location: ../orders-page.php?success=1");
    exit;

} catch (Exception $e) {
    $conn->rollBack();
    echo "Error: " . $e->getMessage();
    exit;
}
?>
