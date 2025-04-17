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
    <link rel="stylesheet" href="css/navbar.css"> <!-- ✅ Added this line -->

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Afacad:ital,wght@0,400..700;1,400..700&family=Caveat:wght@400..700&family=Dosis:wght@200..800&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <nav class="custom-navbar">
        <div class="logo">
            <img src="assets/web_img/ak-logo.png?v2" alt="Adorable Knots Logo">
        </div>

        <div class="nav-links">
            <button> 
                <img src="assets/icons/home.png" alt="">
                <a href="home.php">Home</a>
            </button>
            <button> 
                <img src="assets/icons/bag.png" alt="">
                <a href="store-page.php">Shop Now</a>
            </button>
            <button> 
                <img src="assets/icons/Chat.png" alt="">
                <a href="#">Contact Us</a>
            </button>
            <button class="active"> 
                <img src="assets/icons/user.png" alt="">
                <a href="account-page.php">Account</a>          
            </button>
            <button>
                <img src="assets/icons/cart.png" alt="Cart">
                <a href="#">Cart ( 0 )</a>
            </button>
        </div>
    </nav>

    <div class="logout">
        <a href="login/logout.php">LOGOUT</a>
        <img src="assets/icons/back-white.png" style="transform: scaleX(-1);" alt="">
    </div>

    <div class="container">
        <div class="account-header">Your Account</div>
        <form class="account-container" action="update-account.php" method="post" id="accountForm">

            <!-- FULL NAME DISPLAY ROW -->
            <div class="info-row">
                <label><strong>Full Name:</strong></label>
                <div class="input-group">
                    <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" readonly class="readonly-input">
                </div>
                <div class="button-group">
                    <button type="button" class="edit-btn" data-field="fullname" data-state="edit">
                        <img src="assets/icons/edit.png" alt="Edit" class="icon-btn">
                    </button>
                    <button type="button" class="save-btn" data-field="fullname" style="display: none;">
                        <img src="assets/icons/save.png" alt="Save" class="icon-btn">
                    </button>
                </div>
            </div>

            <!-- HIDDEN FIRST & LAST NAME EDIT ROWS -->
            <div id="fullname-edit-wrapper" style="display: none;">
                <div class="info-row">
                    <label for="firstname">First Name:</label>
                    <div class="input-group">
                        <input type="text" name="firstname" id="firstname" value="">
                    </div>
                </div>
                <div class="info-row">
                    <label for="lastname">Last Name:</label>
                    <div class="input-group">
                        <input type="text" name="lastname" id="lastname" value="">
                    </div>
                </div>
            </div>


            <div class="info-row">
                <label><strong>Username:</strong></label>
                <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" readonly>
                <button type="button" class="edit-btn" data-field="username" data-state="edit">
                    <img src="assets/icons/edit.png" alt="Edit" class="icon-btn">
                </button>
                <button type="button" class="save-btn" data-field="username" style="display: none;">
                    <img src="assets/icons/save.png" alt="Save" class="icon-btn">
                </button>
            </div>

            <div class="info-row">
                <label><strong>Email:</strong></label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                <button type="button" class="edit-btn" data-field="email" data-state="edit">
                    <img src="assets/icons/edit.png" alt="Edit" class="icon-btn">
                </button>
                <button type="button" class="save-btn" data-field="email" style="display: none;">
                    <img src="assets/icons/save.png" alt="Save" class="icon-btn">
                </button>
            </div>

            <div class="info-row">
                <label><strong>Phone Number:</strong></label>
                <input type="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" readonly>
                <button type="button" class="edit-btn" data-field="phone" data-state="edit">
                    <img src="assets/icons/edit.png" alt="Edit" class="icon-btn">
                </button>
                <button type="button" class="save-btn" data-field="phone" style="display: none;">
                    <img src="assets/icons/save.png" alt="Save" class="icon-btn">
                </button>
            </div>

            <!-- Button trigger modal -->
            <button type="button" class="change-password-btn" data-bs-toggle="modal" data-bs-target="#changePassword">
            Change Password
            </button>

            <!-- Modal -->
            <div class="modal fade" id="changePassword" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="changePasswordLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="changePasswordLabel">Modal title</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            body daw
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Understood</button>
                        </div>
                    </div>
                </div>
            </div>
          

        </form>
    </div>

    <script>
    document.querySelectorAll('.edit-btn').forEach(editBtn => {
        editBtn.addEventListener('click', () => {
            const field = editBtn.dataset.field;
            const state = editBtn.dataset.state;
            const saveBtn = document.querySelector(`.save-btn[data-field="${field}"]`);
            const icon = editBtn.querySelector('img');

            if (field === 'fullname') {
                const fullInput = document.querySelector('input[name="fullname"]');
                const editWrapper = document.getElementById('fullname-edit-wrapper');
                const firstInput = document.getElementById('firstname');
                const lastInput = document.getElementById('lastname');

                if (state === 'edit') {
                    const [first, ...lastParts] = fullInput.value.split(' ');
                    firstInput.value = first;
                    lastInput.value = lastParts.join(' ');

                    editWrapper.style.display = 'block';
                    saveBtn.style.display = 'inline-block';
                    icon.src = 'assets/icons/cancel.png';
                    icon.alt = 'Cancel';
                    editBtn.dataset.state = 'cancel';
                } else {
                    editWrapper.style.display = 'none';
                    saveBtn.style.display = 'none';
                    icon.src = 'assets/icons/edit.png';
                    icon.alt = 'Edit';
                    editBtn.dataset.state = 'edit';
                }
            } else {
                // default for other fields
                const input = document.querySelector(`input[name="${field}"]`);
                if (state === 'edit') {
                    input.removeAttribute('readonly');
                    input.focus();
                    saveBtn.style.display = 'inline-block';
                    icon.src = 'assets/icons/cancel.png';
                    icon.alt = 'Cancel';
                    editBtn.dataset.state = 'cancel';
                } else {
                    input.setAttribute('readonly', true);
                    saveBtn.style.display = 'none';
                    icon.src = 'assets/icons/edit.png';
                    icon.alt = 'Edit';
                    editBtn.dataset.state = 'edit';
                }
            }
        });
    });


    document.querySelectorAll('.save-btn').forEach(saveBtn => {
        saveBtn.addEventListener('click', () => {
            const field = saveBtn.dataset.field;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'account/update-account.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function () {
                if (xhr.status === 200) {
                    if (field === 'fullname') {
                        const fullInput = document.querySelector('input[name="fullname"]');
                        const first = document.getElementById('firstname').value;
                        const last = document.getElementById('lastname').value;
                        fullInput.value = `${first} ${last}`;

                        document.getElementById('fullname-edit-wrapper').style.display = 'none';
                    } else {
                        document.querySelector(`input[name="${field}"]`).setAttribute('readonly', true);
                    }

                    saveBtn.style.display = 'none';
                    const editBtn = document.querySelector(`.edit-btn[data-field="${field}"]`);
                    const icon = editBtn.querySelector('img');
                    icon.src = 'assets/icons/edit.png';
                    icon.alt = 'Edit';
                    editBtn.dataset.state = 'edit';
                } else {
                    alert('Error saving changes.');
                }
            };

            let data = '';
            if (field === 'fullname') {
                const first = encodeURIComponent(document.getElementById('firstname').value);
                const last = encodeURIComponent(document.getElementById('lastname').value);
                data = `field=fullname&firstname=${first}&lastname=${last}`;
            } else {
                const value = encodeURIComponent(document.querySelector(`input[name="${field}"]`).value);
                data = `field=${field}&value=${value}`;
            }

            xhr.send(data);
        });
    });

    </script>



    <style>
        .icon-btn {
            width: 20px;
            height: 20px;
            vertical-align: middle;
        }

    </style>

    <script src="javascript/navbar-icons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

