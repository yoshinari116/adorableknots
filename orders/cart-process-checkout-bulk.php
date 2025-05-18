<?php
session_start();
require_once '../database/db.php';

file_put_contents('debug_log.txt', print_r($_POST['products'], true));

$user_id = $_SESSION['user']['user_id'] ?? null;

if (!$user_id) {
    echo "User not logged in.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Invalid request.";
    exit;
}

$address_id = $_POST['address_id'] ?? null;
$payment_mode = $_POST['payment_mode'] ?? null;
$products = $_POST['products'] ?? [];

if (!$address_id || !$payment_mode || empty($products)) {
    echo "Missing data.";
    exit;
}

try {   
    $conn->beginTransaction();

    // Insert order
    $insertOrder = $conn->prepare("
        INSERT INTO orders_tbl (user_id, address_id, payment_mode, order_date, status)
        VALUES (:user_id, :address_id, :payment_mode, NOW(), 'Pending')
    ");
    $insertOrder->execute([
        ':user_id' => $user_id,
        ':address_id' => $address_id,
        ':payment_mode' => $payment_mode
    ]);
    $order_id = $conn->lastInsertId();

    $insertDetail = $conn->prepare("
        INSERT INTO order_details_tbl (order_id, product_id, quantity, customization)
        VALUES (:order_id, :product_id, :quantity, :customization)
    ");

    $deleteCart = $conn->prepare("DELETE FROM cart_tbl WHERE cart_id = :cart_id AND user_id = :user_id");

    foreach ($products as $product_id => $productData) {
        $quantity = $productData['quantity'] ?? 1;
        $customization = $productData['customization'] ?? '';
        $cart_id = $productData['cart_id'] ?? null;

        if (!$product_id || !$cart_id) {
            continue;
        }

        $insertDetail->execute([
            ':order_id' => $order_id,
            ':product_id' => $product_id,
            ':quantity' => $quantity,
            ':customization' => $customization
        ]);

        $deleteCart->execute([
            ':cart_id' => $cart_id,
            ':user_id' => $user_id
        ]);
    }


    $conn->commit();
    unset($_SESSION['cart']);
    header("Location: ../orders-page.php?checkout=success");
    exit;

} catch (Exception $e) {
    $conn->rollBack();
    echo "Error: " . $e->getMessage();
}
