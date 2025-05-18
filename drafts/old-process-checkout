<?php
session_start();
require_once '../database/db.php';

if (!isset($_SESSION['user']['user_id'])) {
    header("Location: signup-page.php");
    exit;
}

$user_id = $_SESSION['user']['user_id'];

// Validate common fields
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

$customization = substr(trim($_POST['customization'] ?? ''), 0, 255); // Limit to 255 chars
$from_cart = isset($_POST['from_cart']) && $_POST['from_cart'] === 'true';

// Validate address ownership
$stmt = $conn->prepare("SELECT * FROM address_tbl WHERE address_id = ? AND user_id = ?");
$stmt->execute([$address_id, $user_id]);
if ($stmt->rowCount() === 0) {
    echo "Invalid or unauthorized address ID.";
    exit;
}

try {
    $conn->beginTransaction();

    $total_price = 0;
    $items = [];

    if ($from_cart && isset($_SESSION['cart'])) {
        $merged_cart = [];

        // Merge duplicate products by summing their quantities
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            if (isset($merged_cart[$product_id])) {
                $merged_cart[$product_id] += $quantity;
            } else {
                $merged_cart[$product_id] = $quantity;
            }
        }

        // Fetch product prices and prepare items
        foreach ($merged_cart as $product_id => $quantity) {
            $product_id = trim($product_id); // sanitize
            
            if ($product_id === '') {
                echo "Empty product_id detected.";
                continue;
            }
        
            $stmt = $conn->prepare("SELECT product_price FROM product_tbl WHERE product_id = :product_id");
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if ($product) {
                $price = $product['product_price'];
                $total_price += $price * $quantity;
        
                $customization = '';
                if (isset($_POST['products'][$product_id]['customization'])) {
                    $customization = $_POST['products'][$product_id]['customization'];
                }
        
                $items[] = [
                    'product_id' => $product_id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'customization' => $customization
                ];
            } else {
                echo "Product not found for ID: $product_id";
            }
        }
        
    } else {
        // Single product checkout
        if (!isset($_POST['products'][0]['product_price'], $_POST['products'][0]['quantity'])) {
            echo "Invalid form submission. Missing product_price or quantity in products.";
            exit;
        }

        $product_data = $_POST['products'][0];
        $product_id = $_POST['product_id'];
        $product_price = floatval($product_data['product_price']);
        $quantity = intval($product_data['quantity']);
        $total_price = $product_price * $quantity;

        $items[] = [
            'product_id' => $product_id,
            'quantity' => $quantity,
            'price' => $product_price
        ];
    }

    // Generate unique alphanumeric order_id starting with 0
    $order_id = '0' . substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 7);

    // Insert order
    $order_sql = "INSERT INTO orders_tbl (order_id, user_id, address_id, payment_mode, total_price, order_status, payment_status, created_at)
                VALUES (:order_id, :user_id, :address_id, :payment_mode, :total_price, 'Pending', 'Pending', NOW())";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->execute([
        ':order_id' => $order_id,
        ':user_id' => $user_id,
        ':address_id' => $address_id,
        ':payment_mode' => $payment_mode,
        ':total_price' => $total_price
    ]);

    // Insert order items
    $item_sql = "INSERT INTO order_items_tbl (item_id, order_id, product_id, quantity, price, customization)
                 VALUES (:item_id, :order_id, :product_id, :quantity, :price, :customization)";
    $item_stmt = $conn->prepare($item_sql);

    if (empty($items)) {
        echo "No items to insert.";
        exit;
    }

    foreach ($items as $item) {
        if (!$item['product_id']) {
            echo "Invalid product_id before insert.";
            continue;
        }
    
        $item_id = 'IT' . substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6);
        $item_stmt->execute([
            ':item_id' => $item_id,
            ':order_id' => $order_id,
            ':product_id' => $item['product_id'],
            ':quantity' => $item['quantity'],
            ':price' => $item['price'],
            ':customization' => $item['customization']
        ]);
    }
    

    $conn->commit();

    if ($from_cart) {
        unset($_SESSION['cart']);
    }

    header("Location: ../orders-page.php?success=1");
    exit;

} catch (Exception $e) {
    $conn->rollBack();
    echo "Error: " . $e->getMessage();
    exit;
}
?>
