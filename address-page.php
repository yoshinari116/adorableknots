<?php
session_start();
include('database/db.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login-page.php');
    exit;
}

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
} else {
    header('Location: login-page.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adorable Knots</title>

    <!-- Styles -->
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/account-page.css">
    <link rel="stylesheet" href="css/address-page.css">
    <link rel="stylesheet" href="css/navbar.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Afacad:ital,wght@0,400..700;1,400..700&family=Caveat:wght@400..700&family=Dosis:wght@200..800&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css"> <!-- also updated -->
</head>

<body>
    <nav class="custom-navbar">
        <div class="logo">
            <img src="assets/web_img/ak-logo.png?v2" alt="Adorable Knots Logo">
        </div>

        <div class="nav-links">
            <button><img src="assets/icons/home.png"><a href="home.php">Home</a></button>
            <button><img src="assets/icons/bag.png"><a href="store-page.php">Shop Now</a></button>
            <button><img src="assets/icons/Chat.png"><a href="#">Contact Us</a></button>
            <button class="active"><img src="assets/icons/user.png"><a href="account-page.php">Account</a></button>
            <button><img src="assets/icons/cart.png"><a href="#">Cart ( 0 )</a></button>
        </div>
    </nav>

    <div class="logout">
        <a href="login/logout.php">LOGOUT</a>
        <img src="assets/icons/back-white.png" style="transform: scaleX(-1);" alt="">
    </div>

    <div class="container">
        <div class="account-container">
            <div class="account-nav-links">
                <div class="account-nav-links-header">
                    <img src="assets/icons/user.png" alt="">
                    My Account
                </div>
                <button class="nav-button" onclick="location.href='account-page.php'">Profile</button>
                <button class="nav-button active" onclick="location.href='address-page.php'">Address</button>
                <button class="nav-button" onclick="location.href='#'">Change Password</button>
                <button class="nav-button" onclick="location.href='#'">My Purchases</button>
            </div>

            <div class="address-container">
                <div class="address-header">
                    My Addresses

                    <!-- Add New Address Button -->
                    <button type="button" class="address-btn" data-bs-toggle="modal" data-bs-target="#manageAddress">
                        Add New Address
                    </button>

                    <!-- Address Modal -->
                    <div class="modal fade" id="manageAddress" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="manageAddressLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered custom-modal">
                            <div class="modal-content">
                                <form id="addressForm" action="account/add-address.php" method="POST">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="manageAddressLabel">Add New Address</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <input type="text" class="form-control" name="phone" placeholder="Phone Number" required pattern="09\d{9}" maxlength="11">
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" class="form-control" name="region" placeholder="Region" required>
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" class="form-control" name="province" placeholder="Province" required>
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" class="form-control" name="city" placeholder="City / Municipality" required>
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" class="form-control" name="barangay" placeholder="Barangay" required>
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" class="form-control" name="postal_code" placeholder="Postal Code" required>
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" class="form-control" name="street_details" placeholder="Street Name, Building, House No." required>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save Address</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- address modal -->

                </div>

                <?php
                $user_id = $user['user_id'];

                $query = "SELECT * FROM address_tbl WHERE user_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute([$user_id]);
                $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <div class="display-addresses-container">
                    <?php foreach ($addresses as $row): 
                        $fullAddress = "{$row['street_details']}<br>{$row['barangay']}, {$row['city']}, {$row['province']}, {$row['region']}, {$row['postal_code']}";
                    ?>
                        <div class="display-address">
                            <p><?= htmlspecialchars($row['phone']) ?></p>
                            <p><?= $fullAddress ?></p> <!-- Use $fullAddress directly to allow <br> to work -->
                            <?php if (!empty($row['IsDefault']) && $row['IsDefault']): ?>
                                <span class="default-badge">Default Address</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>



            </div>
        </div>
    </div>

    <style>
        .icon-btn {
            width: 20px;
            height: 20px;
            vertical-align: middle;
        }
    </style>

    <script src="javascript/navbar-icons.js"></script>
    <script src="javascript/validate-phone.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
