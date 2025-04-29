<?php
session_start();
include('../database/db.php');

if (!isset($_POST['address_id']) || !isset($_SESSION['user'])) {
    header('Location: ../address-page.php');
    exit;
}

$address_id = $_POST['address_id'];
$user_id = $_SESSION['user']['user_id'];
$phone = preg_replace('/\s+/', '', $_POST['phone']);
$region = $_POST['region'];
$province = $_POST['province'];
$city = $_POST['city'];
$barangay = $_POST['barangay'];
$postal_code = $_POST['postal_code'];
$street_details = $_POST['street_details'];

$query = "UPDATE address_tbl SET phone = ?, region = ?, province = ?, city = ?, barangay = ?, postal_code = ?, street_details = ? WHERE address_id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$phone, $region, $province, $city, $barangay, $postal_code, $street_details, $address_id, $user_id]);

header('Location: ../address-page.php');
exit;
