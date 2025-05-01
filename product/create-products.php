<?php
session_start();
require_once '../database/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $product_name = htmlspecialchars($_POST['product_name']);
    $category_id = $_POST['category_id'];
    $product_price = $_POST['product_price'];
    $product_status = $_POST['product_status'];
    $product_customizations = $_POST['customizations'] ?? null;

    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($_FILES["product_img"]["name"]);
    $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $allowed_image_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($image_file_type, $allowed_image_types)) {
        $_SESSION['error'] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        header("Location: ../admin/admin-page.php");
        exit();
    }

    if ($_FILES["product_img"]["size"] > 25 * 1024 * 1024) {
        $_SESSION['error'] = "Your image file is too large. Max: 25MB.";
        header("Location: ../admin/admin-page.php");
        exit();
    }
    

    if (move_uploaded_file($_FILES["product_img"]["tmp_name"], $target_file)) {
        $product_img = basename($_FILES["product_img"]["name"]);

        $sql = "INSERT INTO product_tbl (product_name, category_id, product_price, product_status, product_img, product_customizations)
        VALUES (:product_name, :category_id, :product_price, :product_status, :product_img, :product_customizations)";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':product_name', $product_name);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':product_price', $product_price);
        $stmt->bindParam(':product_status', $product_status);
        $stmt->bindParam(':product_img', $product_img);
        $stmt->bindParam(':product_customizations', $product_customizations);


        if ($stmt->execute()) {
            $_SESSION['success'] = "New product added successfully!";
        } else {
            $_SESSION['error'] = "Database error: " . $stmt->errorInfo()[2];
        }
    } else {
        $_SESSION['error'] = "There was an error uploading your image.";
    }

    header("Location: ../admin/admin-page.php");
    exit();
}
?>
