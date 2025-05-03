<?php
session_start();
require_once 'database/db.php';

$login_user = $_SESSION['user'] ?? null;

// Fetch categories
$sql_categories = "SELECT * FROM category_tbl ORDER BY category_name ASC";
$stmt_categories = $conn->prepare($sql_categories);
$categories = [];

if ($stmt_categories->execute()) {
    $categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "Error fetching categories: " . $stmt_categories->errorInfo()[2];
}

// Ensure 'Others' category is present
$others_exists = false;
foreach ($categories as $category) {
    if ($category['category_name'] === 'Others') {
        $others_exists = true;
        break;
    }
}
if (!$others_exists) {
    $categories[] = ['category_name' => 'Others', 'category_id' => '14'];
}

// Sort categories: push 'Others' last
usort($categories, function ($a, $b) {
    if ($a['category_name'] === 'Others') return 1;
    if ($b['category_name'] === 'Others') return -1;
    return strcmp($a['category_name'], $b['category_name']);
});

// Handle filters
$category_filter = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;
$status_filter = isset($_GET['status']) ? htmlspecialchars($_GET['status']) : null;
$valid_statuses = ['Available', 'Not Available'];
if ($status_filter && !in_array($status_filter, $valid_statuses)) {
    $status_filter = null;
}

// Build dynamic product query
$sql_products = "SELECT p.*, c.category_name 
                 FROM product_tbl p 
                 LEFT JOIN category_tbl c ON p.category_id = c.category_id";

$where_conditions = [];

if ($category_filter) {
    $where_conditions[] = "p.category_id = :category_id";
}
if ($status_filter) {
    $where_conditions[] = "p.product_status = :product_status";
}

if ($where_conditions) {
    $sql_products .= " WHERE " . implode(" AND ", $where_conditions);
}

$stmt_products = $conn->prepare($sql_products);

if ($category_filter) {
    $stmt_products->bindParam(':category_id', $category_filter, PDO::PARAM_INT);
}
if ($status_filter) {
    $stmt_products->bindParam(':product_status', $status_filter, PDO::PARAM_STR);
}

$stmt_products->execute();
$products = $stmt_products->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adorable Knots</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Afacad&family=Caveat&family=Dosis&display=swap" rel="stylesheet">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/store-page.css">
</head>

<body>
    <nav class="custom-navbar">
        <div class="logo">
            <img src="assets/web_img/ak-logo.png?v2" alt="Adorable Knots Logo">
        </div>

        <div class="nav-links">
            <button><img src="assets/icons/home.png"><a href="home.php">Home</a></button>
            <button class="active"><img src="assets/icons/bag.png"><a href="store-page.php">Shop Now</a></button>
            <button><img src="assets/icons/order.png"><a href="<?= isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'orders-page.php' : 'signup-page.php' ?>">My Orders</a></button>
            <button><img src="assets/icons/user.png"><a href="<?= isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'account-page.php' : 'signup-page.php' ?>">Account</a></button>
            <button><img src="assets/icons/cart.png"><a href="<?= isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'cart-page.php' : 'signup-page.php' ?>">Cart ( 0 )</a></button>
        </div>

    </nav>

    <div class="d-flex">
        <!-- Sidebar -->
        <div class="category-sidebar">
            <div class="category-sidebar-header">CATEGORIES</div>
            <div class="category-list">
                <?php
                $status_query = isset($_GET['status']) ? '?status=' . urlencode($_GET['status']) : '';
                ?>
                <button class="nav-button <?= !isset($_GET['category_id']) ? 'active' : '' ?>" onclick="location.href='store-page.php<?= $status_query ?>'">All Products</button>
                <?php foreach ($categories as $category): ?>
                    <button class="nav-button <?= (isset($_GET['category_id']) && $_GET['category_id'] == $category['category_id']) ? 'active' : '' ?>" onclick="location.href='store-page.php?category_id=<?= $category['category_id'] ?><?= $status_query ?>'">
                        <?= htmlspecialchars($category['category_name']) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="category-sidebar-header">FILTER PRODUCTS BY STATUS</div>
            <div class="filter-status-buttons">
                <?php
                $category_query = isset($_GET['category_id']) ? '&category_id=' . urlencode($_GET['category_id']) : '';
                $all_status_href = 'store-page.php' . (isset($_GET['category_id']) ? '?category_id=' . urlencode($_GET['category_id']) : '');
                ?>
                <button class="nav-button <?= !isset($_GET['status']) ? 'active' : '' ?>" onclick="location.href='<?= $all_status_href ?>'">All Status</button>
                <button class="nav-button <?= ($_GET['status'] ?? '') === 'Available' ? 'active' : '' ?>" onclick="location.href='store-page.php?status=Available<?= $category_query ?>'">Available</button>
                <button class="nav-button <?= ($_GET['status'] ?? '') === 'Not Available' ? 'active' : '' ?>" onclick="location.href='store-page.php?status=Not Available<?= $category_query ?>'">Not Available</button>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="products-container container py-4">
            <div class="products-container-header"><h1>OUR PRODUCTS</h1></div>
            <div class="product-grid">
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product-card" data-id="<?= $product["product_id"] ?>" 
                                                data-name="<?= htmlspecialchars($product["product_name"]) ?>" 
                                                data-price="<?= number_format($product["product_price"], 2) ?>" 
                                                data-description="<?= nl2br(htmlspecialchars($product["product_description"])) ?>" 
                                                data-image="uploads/<?= htmlspecialchars($product["product_img"]) ?>" 
                                                data-delivery="<?= htmlspecialchars($product["estimated_delivery"]) ?>" 
                                                data-status="<?= htmlspecialchars($product["product_status"]) ?>">
                            <img class="product-image" src="uploads/<?= htmlspecialchars($product['product_img']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
                            <div class="card-body">
                                <div class="product-name"><?= htmlspecialchars($product['product_name']) ?></div>
                                <div class="product-price">₱<?= number_format($product['product_price'], 2) ?></div>
                                <div class="product-description"><?= nl2br(htmlspecialchars($product['product_description'])) ?></div>
                                <div class="estimated-delivery">Estimated Delivery: <?= htmlspecialchars($product['estimated_delivery']) ?></div>
                                <?php if ($product['product_status'] === 'Not Available'): ?>
                                    <div class="status not-available"><span class="badge">Not Available</span></div>
                                <?php endif; ?>
                            </div>
                        </div>

                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No products found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="productModal" class="modal" style="display: none; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content position-relative text-center p-4">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" onclick="closeProductModal()"></button>
                <img id="modalImage" src="" class="img-fluid mx-auto d-block mb-3" style="max-height: 300px;">
                <div class="modal-product-info text-start">
                    <h2 id="modalName" class="modal-name mb-2"></h2>
                    <p  class="product-price"><strong>Price:</strong> ₱<span id="modalPrice"></span></p>
                    <p id="modalDescription" class="modal-description"></p>
                    <p class="modal-delivery"><strong>Estimated Delivery:</strong> <span id="modalDelivery"></span></p>
                    <p id="modalStatus" class="modal-status text-danger d-none">Not Available</p>
                </div>
                <div class="modal-footer justify-content-center mt-4">
                    <button id="addToCartBtn" class="btn btn-secondary" style="color: white;">Add to Cart</button>
                    <button id="checkoutBtn" class="btn" style="background-color: #FF7EBC; color: white;">Checkout</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
        <script>
        let selectedProduct = null;
        let isLoggedIn = <?= json_encode(isset($_SESSION['logged_in']) && $_SESSION['logged_in']) ?>;

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.product-card').forEach(card => {
                card.addEventListener('click', function () {
                    const product = {
                        id: this.getAttribute('data-id'),
                        name: this.getAttribute('data-name'),
                        price: this.getAttribute('data-price'),
                        description: this.getAttribute('data-description'),
                        image: this.getAttribute('data-image'),
                        delivery: this.getAttribute('data-delivery'),
                        status: this.getAttribute('data-status')
                    };

                    showProductModal(product);
                });
            });

            document.getElementById('checkoutBtn').addEventListener('click', handleCheckout);
            document.getElementById('addToCartBtn').addEventListener('click', handleAddToCart);
        });

        function showProductModal(product) {
            selectedProduct = product;
            document.getElementById('modalImage').src = product.image;
            document.getElementById('modalName').innerText = product.name;
            document.getElementById('modalPrice').innerText = product.price;
            document.getElementById('modalDescription').innerHTML = product.description;
            document.getElementById('modalDelivery').innerText = product.delivery;

            const statusEl = document.getElementById('modalStatus');
            statusEl.classList.toggle('d-none', product.status !== 'Not Available');

            document.getElementById('productModal').style.display = 'block';
        }

        function closeProductModal() {
            document.getElementById('productModal').style.display = 'none';
        }

        function handleCheckout() {
            if (!selectedProduct || selectedProduct.status === 'Not Available') {
                alert('Product is not available for checkout.');
                return;
            }

            if (isLoggedIn) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'checkout-page.php';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'id';
                input.value = selectedProduct.id;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            } else {
                window.location.href = 'signup-page.php';
            }
        }

        function handleAddToCart() {
            if (!selectedProduct || selectedProduct.status === 'Not Available') {
                alert('Product is not available.');
                return;
            }

            if (!isLoggedIn) {
                window.location.href = 'signup-page.php';
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'cart-page.php';

            const productIdInput = document.createElement('input');
            productIdInput.type = 'hidden';
            productIdInput.name = 'product_id';
            productIdInput.value = selectedProduct.id;

            const quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = 'quantity';
            quantityInput.value = '1';

            form.appendChild(productIdInput);
            form.appendChild(quantityInput);

            document.body.appendChild(form);
            form.submit();
        }
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="javascript/navbar-icons.js"></script>
</body>
</html>
