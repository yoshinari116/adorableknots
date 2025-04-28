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
    
    // Sanitize input to handle special characters
    $phone = htmlspecialchars($_POST['phone'] ?? '');
    $region = htmlspecialchars($_POST['region'] ?? '');
    $province = htmlspecialchars($_POST['province'] ?? '');
    $city = htmlspecialchars($_POST['city'] ?? '');
    $barangay = htmlspecialchars($_POST['barangay'] ?? '');
    $postal_code = htmlspecialchars($_POST['postal_code'] ?? '');
    $street_details = htmlspecialchars($_POST['street_details'] ?? '');

    // Simple validation
    if (
        empty($phone) || empty($region) || empty($province) || empty($city) ||
        empty($barangay) || empty($postal_code) || empty($street_details)
    ) {
        // Redirect back to address page with error message in URL
        header('Location: ../address-page.php?error=1');
        exit;
    }

    try {
        // Set character set for the connection
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->exec("SET NAMES 'utf8mb4'");

        // Prepare the SQL query
        $stmt = $conn->prepare("INSERT INTO address_tbl 
            (user_id, phone, region, province, city, barangay, postal_code, street_details) 
            VALUES 
            (:user_id, :phone, :region, :province, :city, :barangay, :postal_code, :street_details)");

        // Bind parameters
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':region', $region);
        $stmt->bindParam(':province', $province);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':barangay', $barangay);
        $stmt->bindParam(':postal_code', $postal_code);
        $stmt->bindParam(':street_details', $street_details);

        // Execute the query
        if ($stmt->execute()) {
            header('Location: ../address-page.php?success=1');
            exit;
        } else {
            header('Location: ../address-page.php?error=1');
            exit;
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo 'Database error: ' . $e->getMessage();
    }
}
?>
