<?php
require_once '../database/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Fetch the image of the product to delete
    $stmt = $conn->prepare("SELECT product_img FROM product_tbl WHERE product_id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Delete the product record
    $stmt = $conn->prepare("DELETE FROM product_tbl WHERE product_id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // If there's an image, delete it from the uploads folder
    if ($product['product_img']) {
        $image_path = "../uploads/" . $product['product_img'];
        if (file_exists($image_path)) {
            unlink($image_path); // Delete the image file
        }
    }

    // Redirect to the admin page with success message
    $_SESSION['success'] = "Product deleted successfully!";
    header("Location: ../admin/admin-page.php");
    exit();
}
?>
