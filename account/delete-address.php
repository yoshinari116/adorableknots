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

    $stmt = $conn->prepare("DELETE FROM address_tbl WHERE address_id = ? AND user_id = ?");
    $stmt->execute([$addressId, $userId]);

    header('Location: ../address-page.php');
    exit;
} else {
    header('Location: ../address-page.php');
    exit;
}
?>
