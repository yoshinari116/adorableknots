<?php
session_start();
require_once '../database/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = trim($_POST['category_name']);
    
    // Check if the category already exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM category_tbl WHERE category_name = :category_name");
    $stmt->bindParam(':category_name', $category_name, PDO::PARAM_STR);
    $stmt->execute();
    $category_exists = $stmt->fetchColumn() > 0;

    if ($category_exists) {
        // If category exists, show error message
        $_SESSION['error'] = 'Invalid: Category name already exists.';
        header('Location: ../admin/admin-page.php'); // Redirect to the page where the modal is
        exit();
    }

    // Proceed to add the category if it doesn't exist
    $stmt = $conn->prepare("INSERT INTO category_tbl (category_name) VALUES (:category_name)");
    $stmt->bindParam(':category_name', $category_name, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Category added successfully.';
    } else {
        $_SESSION['error'] = 'Error adding category.';
    }

    header('Location: ../admin/admin-page.php');
    exit();
}
?>
