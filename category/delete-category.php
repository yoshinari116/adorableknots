<?php
session_start();
require_once '../database/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_id = $_POST['category_id'];

    // Check if the category is "Others" (by category_id or name)
    $stmt_check = $conn->prepare("SELECT category_name FROM category_tbl WHERE category_id = :category_id");
    $stmt_check->bindParam(':category_id', $category_id, PDO::PARAM_STR);
    $stmt_check->execute();
    $category = $stmt_check->fetch(PDO::FETCH_ASSOC);

    // If the category is "Others", do not allow deletion
    if ($category && $category['category_name'] === 'Others') {
        $_SESSION['error'] = "The 'Others' category cannot be deleted.";
        header('Location: ../admin/admin-page.php');
        exit();
    }

    // Proceed to delete the category if it's not "Others"
    $stmt = $conn->prepare("DELETE FROM category_tbl WHERE category_id = :category_id");
    $stmt->bindParam(':category_id', $category_id, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Category deleted successfully.';
    } else {
        $_SESSION['error'] = 'Error deleting category.';
    }

    header('Location: ../admin/admin-page.php');
    exit();
}
?>
