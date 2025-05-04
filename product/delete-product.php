<?php
require_once '../database/db.php';
session_start();

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Validate the alphanumeric product ID format (e.g., PRODUCT25050410)
    if (empty($id) || !preg_match('/^PRODUCT[0-9]{8}$/', $id)) {
        $_SESSION['error'] = "Invalid product ID format.";
        header("Location: ../admin/admin-page.php");
        exit();
    }

    try {
        // Fetch the image of the product to delete
        $stmt = $conn->prepare("SELECT product_img FROM product_tbl WHERE product_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            // Delete the product record
            $stmt = $conn->prepare("DELETE FROM product_tbl WHERE product_id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_STR);
            $stmt->execute();

            // If there's an image, delete it from the uploads folder
            if ($product['product_img']) {
                $image_path = "../uploads/" . $product['product_img'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }

            $_SESSION['success'] = "Product deleted successfully!";
        } else {
            $_SESSION['error'] = "Product not found.";
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "An error occurred while deleting the product: " . $e->getMessage();
    }

    header("Location: ../admin/admin-page.php");
    exit();
}
?>
