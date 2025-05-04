<?php
session_start();
include('../database/db.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../login-page.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['address_id'])) {
    $userId = $_SESSION['user']['user_id'];
    $addressId = $_POST['address_id'];

    try {
        // Begin transaction to handle multiple queries
        $conn->beginTransaction();

        // Check if the deleted address was set as default
        $stmt = $conn->prepare("SELECT IsDefault FROM address_tbl WHERE address_id = ? AND user_id = ?");
        $stmt->execute([$addressId, $userId]);
        $address = $stmt->fetch();

        if ($address && $address['IsDefault'] == 1) {
            // If the deleted address was the default, find the last address for this user
            $stmt = $conn->prepare("SELECT address_id FROM address_tbl WHERE user_id = ? ORDER BY address_id DESC LIMIT 1");
            $stmt->execute([$userId]);
            $lastAddress = $stmt->fetch();

            if ($lastAddress) {
                // Set the last address as the default
                $updateStmt = $conn->prepare("UPDATE address_tbl SET IsDefault = 1 WHERE address_id = ?");
                $updateStmt->execute([$lastAddress['address_id']]);
            }
        }

        // Delete the selected address
        $stmt = $conn->prepare("DELETE FROM address_tbl WHERE address_id = ? AND user_id = ?");
        $stmt->execute([$addressId, $userId]);

        // Commit the transaction
        $conn->commit();

        header('Location: ../address-page.php');
        exit;

    } catch (PDOException $e) {
        // If any error occurs, rollback the transaction
        $conn->rollBack();
        http_response_code(500);
        echo 'Database error: ' . $e->getMessage();
    }
} else {
    header('Location: ../address-page.php');
    exit;
}
?>
