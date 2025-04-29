<?php
session_start();
include('../database/db.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(403);
    echo 'Unauthorized access.';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['address_id'])) {
    $user_id = $_SESSION['user']['user_id'];
    $address_id = $_POST['address_id'];

    try {
        // Reset all to not default
        $stmt = $conn->prepare("UPDATE address_tbl SET IsDefault = 0 WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Set selected as default
        $stmt = $conn->prepare("UPDATE address_tbl SET IsDefault = 1 WHERE user_id = ? AND address_id = ?");
        $stmt->execute([$user_id, $address_id]);

        header('Location: ../address-page.php');
        exit;

    } catch (PDOException $e) {
        http_response_code(500);
        echo 'Database error: ' . $e->getMessage();
        exit;
    }
} else {
    http_response_code(400);
    echo 'Invalid request.';
}
