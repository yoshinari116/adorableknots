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
    $phone = $_POST['phone'] ?? '';
    $region = $_POST['region'] ?? '';
    $province = $_POST['province'] ?? '';
    $city = $_POST['city'] ?? '';
    $barangay = $_POST['barangay'] ?? '';
    $postal_code = $_POST['postal_code'] ?? '';
    $street_details = $_POST['street_details'] ?? '';

    // Simple validation
    if (
        empty($phone) || empty($region) || empty($province) || empty($city) ||
        empty($barangay) || empty($postal_code) || empty($street_details)
    ) {
        http_response_code(400);
        echo 'All fields are required.';
        exit;
    }

    try {
        $stmt = $conn->prepare("INSERT INTO address_tbl 
            (user_id, phone, region, province, city, barangay, postal_code, street_details) 
            VALUES 
            (:user_id, :phone, :region, :province, :city, :barangay, :postal_code, :street_details)");
        
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':region', $region);
        $stmt->bindParam(':province', $province);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':barangay', $barangay);
        $stmt->bindParam(':postal_code', $postal_code);
        $stmt->bindParam(':street_details', $street_details);

        if ($stmt->execute()) {
            header('Location: ../address-page.php?success=1');
            exit;
        } else {
            http_response_code(500);
            echo 'Failed to save address.';
        }

    } catch (PDOException $e) {
        http_response_code(500);
        echo 'Database error: ' . $e->getMessage();
    }
}
?>
