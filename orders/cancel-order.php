<?php
session_start();
require_once '../database/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user']['user_id'];
$order_id = $_POST['order_id'] ?? '';

if (!$order_id) {
    echo json_encode(['success' => false, 'message' => 'Order ID missing']);
    exit;
}

// Check if order exists and belongs to this user
$sql = "SELECT order_status FROM orders_tbl WHERE order_id = :order_id AND user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->execute(['order_id' => $order_id, 'user_id' => $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit;
}

$status = strtolower($order['order_status']);
if (!in_array($status, ['pending', 'processing'])) {
    echo json_encode(['success' => false, 'message' => 'Order cannot be cancelled at this stage']);
    exit;
}

// Update order status to cancelled
$update_sql = "UPDATE orders_tbl SET order_status = 'cancelled' WHERE order_id = :order_id AND user_id = :user_id";
$update_stmt = $conn->prepare($update_sql);

if ($update_stmt->execute(['order_id' => $order_id, 'user_id' => $user_id])) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update order']);
}
