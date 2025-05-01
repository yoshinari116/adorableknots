<?php
session_start();
require_once '../database/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_id = $_POST['category_id'];

    // Delete the category
    $stmt = $conn->prepare("DELETE FROM category_tbl WHERE category_id = :category_id");
    $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Category deleted successfully.';
    } else {
        $_SESSION['error'] = 'Error deleting category.';
    }

    header('Location: ../admin/admin-page.php');
    exit();
}
?>
