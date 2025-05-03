<?php
require_once '../database/db.php';

if (isset($_POST['update'])) {
    $id = $_POST['product_id'];
    $name = htmlspecialchars($_POST['product_name']);
    $price = $_POST['product_price'];
    $status = $_POST['product_status'];
    $description = $_POST['product_description'];
    $estimated_delivery = $_POST['estimated_delivery'];

    $stmt = $conn->prepare("SELECT product_img FROM product_tbl WHERE product_id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    $update_query = "UPDATE product_tbl SET product_name = :name, product_price = :price, product_status = :status, product_description = :description, estimated_delivery = :estimated_delivery";

    if (!empty($_FILES['product_img']['name'])) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES["product_img"]["name"]);
        $image_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($image_type, $allowed) && move_uploaded_file($_FILES["product_img"]["tmp_name"], $target_file)) {
            $update_query .= ", product_img = :img";
            $img = basename($_FILES["product_img"]["name"]);

            if ($product['product_img'] && $product['product_img'] !== $img) {
                $old_image_path = $target_dir . $product['product_img'];
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }
        }
    }

    $update_query .= " WHERE product_id = :id";
    $stmt = $conn->prepare($update_query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':estimated_delivery', $estimated_delivery);
    $stmt->bindParam(':id', $id);

    if (isset($img)) {
        $stmt->bindParam(':img', $img);
    }

    if ($stmt->execute()) {
        header("Location: ../admin/admin-page.php");
        exit();
    }
}
?>
