<?php
session_start();
require_once '../database/db.php';

if (!isset($_SESSION['user']['user_id'])) {
    echo "User not logged in.";
    exit;
}

$user_id = $_SESSION['user']['user_id'];
$cart_items = $_SESSION['cart'] ?? [];
$address_id = $_POST['address_id'] ?? null;
$payment_mode = $_POST['payment_mode'] ?? null;
$customization = $_POST['customization'] ?? '';

// Check if necessary data is provided
if (!$address_id || !$payment_mode) {
    echo "Required fields missing!";
    exit;
}

// Check if the address_id exists in the address_tbl
$stmt = $conn->prepare("SELECT COUNT(*) FROM address_tbl WHERE address_id = :address_id");
$stmt->bindParam(':address_id', $address_id, PDO::PARAM_INT);
$stmt->execute();
$count = $stmt->fetchColumn();

if ($count == 0) {
    echo "The selected address does not exist.";
    exit;
}

// Start transaction to ensure data integrity
$conn->beginTransaction();

try {
    // Get the status from the first product in the cart (you can adjust this logic)
    $first_product = reset($cart_items);
    $product_id = $first_product['product_id'];

    // Fetch the status of the product from the database (assuming there's a 'product_status' field in the product_tbl)
    $stmt = $conn->prepare("SELECT product_status FROM product_tbl WHERE product_id = :product_id");
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $product_status = $stmt->fetchColumn();

    // If no status is found, default to 'Pending'
    if (!$product_status) {
        $product_status = 'Pending';
    }

    // Insert into the orders table (orders_tbl)
    $stmt = $conn->prepare("INSERT INTO orders_tbl (user_id, address_id, payment_mode, order_status, payment_status, created_at) 
                            VALUES (:user_id, :address_id, :payment_mode, :order_status, :payment_status, NOW())");
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':address_id', $address_id, PDO::PARAM_INT);
    $stmt->bindValue(':payment_mode', $payment_mode, PDO::PARAM_STR);
    $stmt->bindValue(':order_status', $product_status, PDO::PARAM_STR); // Assuming product status is used for order status
    $stmt->bindValue(':payment_status', 'Pending', PDO::PARAM_STR); // Default payment status
    $stmt->execute();

    // Get the last inserted order ID
    $order_id = $conn->lastInsertId();

    // Loop through the cart items to insert them into order_items_tbl
    foreach ($cart_items as $product) {
        $stmt = $conn->prepare("INSERT INTO order_items_tbl (order_id, product_id, quantity, customization)
                                VALUES (:order_id, :product_id, :quantity, :customization)");
        $stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->bindValue(':product_id', $product['product_id'], PDO::PARAM_INT);
        $stmt->bindValue(':quantity', $product['quantity'], PDO::PARAM_INT);
        $stmt->bindValue(':customization', $customization, PDO::PARAM_STR); // Handle customization
        $stmt->execute();
    }

    // Commit the transaction
    $conn->commit();

    // Clear the cart session
    unset($_SESSION['cart']);

    // Redirect to order confirmation or order details page
    header("Location: ../orders/order-confirmation.php?order_id=" . $order_id);
    exit;
} catch (Exception $e) {
    // Rollback the transaction in case of error
    $conn->rollBack();
    echo "Error: " . $e->getMessage();
}
?>
