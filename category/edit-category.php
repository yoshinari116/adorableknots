<?php
session_start();
require_once '../database/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_id = $_POST['category_id'];
    $new_category_name = trim($_POST['new_category_name']);

    // Check if the category being updated is the "Others" category
    $stmt_check = $conn->prepare("SELECT category_name FROM category_tbl WHERE category_id = :category_id");
    $stmt_check->bindParam(':category_id', $category_id, PDO::PARAM_STR);
    $stmt_check->execute();
    $category = $stmt_check->fetch(PDO::FETCH_ASSOC);

    // If the category is "Others", do not allow updating
    if ($category && $category['category_name'] === 'Others') {
        $_SESSION['error'] = "The 'Others' category cannot be updated.";
        header('Location: ../admin/admin-page.php');
        exit();
    }

    // Check if the new category name already exists (excluding the current category being edited)
    $stmt = $conn->prepare("SELECT COUNT(*) FROM category_tbl WHERE category_name = :category_name AND category_id != :category_id");
    $stmt->bindParam(':category_name', $new_category_name, PDO::PARAM_STR);
    $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
    $stmt->execute();
    $category_exists = $stmt->fetchColumn() > 0;

    if ($category_exists) {
        // If category exists, show error message
        $_SESSION['error'] = 'Invalid: Category name already exists.';
        header('Location: ../admin/admin-page.php'); // Redirect back to the page
        exit();
    }

    // Proceed to update the category if it doesn't exist
    $stmt = $conn->prepare("UPDATE category_tbl SET category_name = :new_category_name WHERE category_id = :category_id");
    $stmt->bindParam(':new_category_name', $new_category_name, PDO::PARAM_STR);
    $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Category updated successfully.';
    } else {
        $_SESSION['error'] = 'Error updating category.';
    }

    header('Location: ../admin/admin-page.php');
    exit();
}
?>

