<!-- <?php
session_start();
include '../database/db.php';

if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user'])) {
    header("Location: ../login-page.php");
    exit();
}

$user_id = $_SESSION['user']['user_id'];

$region = trim($_POST['region']);
$province = trim($_POST['province']);
$city = trim($_POST['city']);
$barangay = trim($_POST['barangay']);
$postal_code = trim($_POST['postal_code']);
$street = trim($_POST['street']);
$is_default = isset($_POST['is_default']) ? 1 : 0;

try {
   
    if ($is_default) {
        $stmt = $conn->prepare("UPDATE address_tbl SET IsDefault = 0 WHERE Users_ID = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
    }


    $stmt = $conn->prepare("INSERT INTO address_tbl (Users_ID, Region, Province, City, Barangay, Postal_Code, Street_Details, IsDefault) 
                            VALUES (:user_id, :region, :province, :city, :barangay, :postal_code, :street, :is_default)");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':region', $region);
    $stmt->bindParam(':province', $province);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':barangay', $barangay);
    $stmt->bindParam(':postal_code', $postal_code);
    $stmt->bindParam(':street', $street);
    $stmt->bindParam(':is_default', $is_default);
    $stmt->execute();

    header("Location: ../account-page.php");
    exit();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> -->
