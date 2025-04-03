<?php
session_start();
include('database/db.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login-page.php');
    exit;
}

if (isset($_SESSION['user'])) {
    $login_user = $_SESSION['user'];
} else {
    header('Location: login-page.php');
    exit;
}

// $cart_query = "SELECT COUNT(*) AS cart_count FROM cart_tbl WHERE user_id = :user_id";
// $cart_stmt = $conn->prepare($cart_query);
// $cart_stmt->bindParam(':user_id', $login_user['id']);
// $cart_stmt->execute();
// $cart_count = $cart_stmt->fetch(PDO::FETCH_ASSOC)['cart_count'] ?? 0;

// $order_query = "SELECT COUNT(*) AS order_count FROM order_tbl WHERE id = :user_id";
// $order_stmt = $conn->prepare($order_query);
// $order_stmt->bindParam(':user_id', $login_user['id']);
// $order_stmt->execute();
// $order_count = $order_stmt->fetch(PDO::FETCH_ASSOC)['order_count'] ?? 0;

// $product_query = "SELECT * FROM products_tbl";
// $product_stmt = $conn->query($product_query);
// $products = $product_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

user
