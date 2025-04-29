<?php
session_start();
include('../database/db.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(403);
    echo 'Unauthorized access.';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user']['user_id'];

    // Sanitize input
    $phone = htmlspecialchars($_POST['phone'] ?? '');
    $region = htmlspecialchars($_POST['region'] ?? '');
    $province = htmlspecialchars($_POST['province'] ?? '');
    $city = htmlspecialchars($_POST['city'] ?? '');
    $barangay = htmlspecialchars($_POST['barangay'] ?? '');
    $postal_code = htmlspecialchars($_POST['postal_code'] ?? '');
    $street_details = htmlspecialchars($_POST['street_details'] ?? '');
    $isDefault = isset($_POST['is_default']) ? 1 : 0;

    if (
        empty($phone) || empty($region) || empty($province) || empty($city) ||
        empty($barangay) || empty($postal_code) || empty($street_details)
    ) {
        header('Location: ../address-page.php?error=1');
        exit;
    }

    try {
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->exec("SET NAMES 'utf8mb4'");

        if ($isDefault == 1) {
            $resetDefault = $conn->prepare("UPDATE address_tbl SET IsDefault = 0 WHERE user_id = :user_id");
            $resetDefault->execute(['user_id' => $user_id]);
        }

        $stmt = $conn->prepare("INSERT INTO address_tbl 
            (user_id, phone, region, province, city, barangay, postal_code, street_details, IsDefault) 
            VALUES 
            (:user_id, :phone, :region, :province, :city, :barangay, :postal_code, :street_details, :isDefault)");

        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':region', $region);
        $stmt->bindParam(':province', $province);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':barangay', $barangay);
        $stmt->bindParam(':postal_code', $postal_code);
        $stmt->bindParam(':street_details', $street_details);
        $stmt->bindParam(':isDefault', $isDefault);

        if ($stmt->execute()) {
            header('Location: ../address-page.php?success=1');
            exit;
        } else {
            header('Location: ../address-page.php?error=2');
            exit;
        }

    } catch (PDOException $e) {
        http_response_code(500);
        echo 'Database error: ' . $e->getMessage();
    }
}
?>
