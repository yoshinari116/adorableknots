<?php
session_start();
require_once '../database/db.php';

$sql_categories = "SELECT * FROM category_tbl ORDER BY category_name ASC";
$stmt_categories = $conn->prepare($sql_categories);

if ($stmt_categories->execute()) {
    $categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);
} else {
    $categories = [];
    echo "Error fetching categories: " . $stmt_categories->errorInfo()[2];
}

// Check if 'Others' category exists and if not, add it with a new category_id
$others_exists = false;
foreach ($categories as $category) {
    if ($category['category_name'] === 'Others') {
        $others_exists = true;
        break;
    }
}

if (!$others_exists) {
    // Generate the category_id for "Others"
    $category_name = 'Others';
    $minute = date('i');  // Current minute
    $second = date('s');  // Current second
    $first_letter = strtoupper(substr($category_name, 0, 1)); // First letter of 'Others'

    // Create category_id: CT + minute + second + first letter of category name
    $category_id = 'CT' . $minute . $second . $first_letter;

    // Insert the "Others" category with the generated category_id
    $stmt_insert = $conn->prepare("INSERT INTO category_tbl (category_id, category_name) VALUES (:category_id, :category_name)");
    $stmt_insert->bindParam(':category_id', $category_id, PDO::PARAM_STR);
    $stmt_insert->bindParam(':category_name', $category_name, PDO::PARAM_STR);
    
    if ($stmt_insert->execute()) {
        // Successfully added the 'Others' category
        $_SESSION['success'] = "'Others' category added successfully with ID $category_id.";
    } else {
        // Error adding the 'Others' category
        $_SESSION['error'] = "Error adding 'Others' category.";
    }
}

// Sorting categories: push 'Others' to the last
usort($categories, function($a, $b) {
    if ($a['category_name'] === 'Others') return 1;
    if ($b['category_name'] === 'Others') return -1;
    return strcmp($a['category_name'], $b['category_name']);
});

// Handle filters
$category_filter = isset($_GET['category_id']) ? $_GET['category_id'] : null;
$valid_statuses = ['Available', 'Not Available'];
$status_filter = $_GET['status'] ?? 'Available';  // Default to 'Available'

if (!in_array($status_filter, $valid_statuses)) {
    $status_filter = null;
}

// Build product query
$sql_products = "SELECT p.*, c.category_name 
                 FROM product_tbl p 
                 LEFT JOIN category_tbl c ON p.category_id = c.category_id";

$where_conditions = [];
$params = [];

if ($category_filter) {
    $where_conditions[] = "p.category_id = :category_id";
    $params[':category_id'] = $category_filter;
}

if ($status_filter) {
    $where_conditions[] = "p.product_status = :product_status";
    $params[':product_status'] = $status_filter;
}

if ($where_conditions) {
    $sql_products .= " WHERE " . implode(" AND ", $where_conditions);
}

$stmt_products = $conn->prepare($sql_products);
foreach ($params as $param => $value) {
    $stmt_products->bindValue($param, $value);
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Afacad:ital,wght@0,400..700;1,400..700&family=Caveat:wght@400..700&family=Dosis:wght@200..800&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="../css/admin-page.css">

</head>
<body>

    <nav class="custom-navbar">
        <div class="custom-navbar-header">Admin Page</div>
        <div class="custom-navbar-contents">
            <div class="logo">
            <img src="../assets/web_img/ak-logo.png?v2" alt="Adorable Knots Logo">
            </div>

            <div class="nav-links">
                <button class="active"><a href="admin/admin-page.php">Products</a></button>
                <button><a href="admin-orders-page.php">Orders</a></button>
                <button><a href="#">Analytics</a></button>
            </div>

            <div class="logout">
                <a href="../login/logout.php">LOGOUT</a>
                
            </div>
        </div>
        
    </nav>

    <div class="d-flex">
        <div class="category-sidebar">
            <div class="category-sidebar-header">CATEGORIES</div>
            <div class="category-list">
                <?php
                $status_query = isset($_GET['status']) ? '?status=' . urlencode($_GET['status']) : '';
                ?>

                <?php
                $has_status = isset($_GET['status']);
                $status_query = $has_status ? '?status=' . urlencode($_GET['status']) : '';
                ?>
                <button class="nav-button <?= !isset($_GET['category_id']) ? 'active' : '' ?>" onclick="location.href='admin-page.php<?= $status_query ?>'">
                All Products
                </button>



                <?php foreach ($categories as $category): ?>
                    <button class="nav-button <?= (isset($_GET['category_id']) && $_GET['category_id'] == $category['category_id']) ? 'active' : '' ?>" onclick="location.href='admin-page.php?category_id=<?= $category['category_id'] ?><?= $status_query ?>'">
                        <?= htmlspecialchars($category['category_name']) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="category-sidebar-header">FILTER PRODUCTS BY STATUS</div>
            <div class="filter-status-buttons">
                <?php
                $category_query = isset($_GET['category_id']) ? '&category_id=' . urlencode($_GET['category_id']) : '';
                $status = $_GET['status'] ?? 'Available'; // Default to 'Available'

                // Page name (adjust if using this in admin page)
                $page = basename($_SERVER['PHP_SELF']);
                ?>

                <!-- All Status button -->
                <button class="nav-button <?= $status === 'All' ? 'active' : '' ?>" onclick="location.href='<?= $page ?>?status=All<?= $category_query ?>'">
                    All Status
                </button>

                <!-- Available button -->
                <button class="nav-button <?= $status === 'Available' ? 'active' : '' ?>" onclick="location.href='<?= $page ?>?status=Available<?= $category_query ?>'">
                    Available
                </button>

                <!-- Not Available button -->
                <button class="nav-button <?= $status === 'Not Available' ? 'active' : '' ?>" onclick="location.href='<?= $page ?>?status=Not%20Available<?= $category_query ?>'">
                    Not Available
                </button>
            </div>




            <div class="category-sidebar-header">MANAGE CATEGORIES</div>
            <div class="manage-category-buttons">
                <button type="button" class="btn btn-sm manage-category-btn" data-bs-toggle="modal" data-bs-target="#addCategoryModal">Add New Category</button>
                <button type="button" class="btn btn-sm manage-category-btn" data-bs-toggle="modal" data-bs-target="#editCategoryModal">Edit Category</button>
                <button type="button" class="btn btn-sm manage-category-btn" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal">Delete Category</button>
            </div>
        </div>
    </div>


    <div class="products-container container py-4">
        <div class="products-container-header">
            <h1>PRODUCT LIST</h1>
            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addproduct">ADD NEW PRODUCT</button>
        </div>

        <div class="product-grid">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $row): ?>
                    <div class="product-card" data-bs-toggle="modal" data-bs-target="#editProductModal<?= $row['product_id'] ?>">
                        <img src="../uploads/<?= htmlspecialchars($row['product_img']) ?>" alt="<?= htmlspecialchars($row['product_name']) ?>" class="product-image">
                        <div class="card-body">
                            <div class="product-name">
                                <?= htmlspecialchars($row['product_name']) ?>
                            </div>
                            <div class="product-price">
                                ₱<?= number_format($row['product_price'], 2) ?>
                            </div>
                            <div class="product-description">
                                <?= nl2br(htmlspecialchars($row['product_description'])) ?>
                            </div> 
                            <div class="estimated-delivery">
                                Estimated Delivery: <?= htmlspecialchars($row['estimated_delivery']) ?>
                            </div>
                            <div class="status <?= $row['product_status'] === 'Available' ? 'available' : 'not-available' ?>">
                                <?= htmlspecialchars($row['product_status']) ?>
                            </div>


                        </div>
                    </div>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editProductModal<?= $row['product_id'] ?>" tabindex="-1" aria-labelledby="editProductModalLabel<?= $row['product_id'] ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="../product/update-product.php" method="POST" enctype="multipart/form-data" class="modal-content" onsubmit="return buildCustomizationJSON(<?= $row['product_id'] ?>)">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editProductModalLabel<?= $row['product_id'] ?>">Edit Product</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>">

                                    <!-- Product Name -->
                                    <div class="mb-3">
                                        <label>Product Name</label>
                                        <input type="text" class="form-control" name="product_name" value="<?= htmlspecialchars($row['product_name']) ?>" required>
                                    </div>

                                    <!-- Category -->
                                    <div class="mb-3">
                                        <label>Category</label>
                                        <select name="category_id" class="form-control" required>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?= $category['category_id']; ?>" <?= $row['category_id'] === $category['category_id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($category['category_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <!-- Price -->
                                    <div class="mb-3">
                                        <label>Price</label>
                                        <input type="number" class="form-control" name="product_price" value="<?= $row['product_price'] ?>" required>
                                    </div>

                                    <!-- Product Description -->
                                    <div class="mb-3">
                                        <label>Product Description</label>
                                        <textarea name="product_description" class="form-control" rows="4"><?= htmlspecialchars($row['product_description']) ?></textarea>
                                    </div>

                                    <!-- Estimated Delivery -->
                                    <div class="mb-3">
                                        <label>Estimated Delivery</label>
                                        <input type="text" name="estimated_delivery" class="form-control" value="<?= htmlspecialchars($row['estimated_delivery']) ?>" placeholder="(e.g. 3 - 5 days)" required />
                                    </div>

                                    <!-- Status -->
                                    <div class="mb-3">
                                        <label>Status</label>
                                        <select name="product_status" class="form-select">
                                            <option value="Available" <?= $row['product_status'] === 'Available' ? 'selected' : '' ?>>Available</option>
                                            <option value="Not Available" <?= $row['product_status'] === 'Not Available' ? 'selected' : '' ?>>Not Available</option>
                                        </select>
                                    </div>

                                    <!-- Change Image -->
                                    <div class="mb-3">
                                        <label>Change Image (optional)</label>
                                        <input type="file" class="form-control" name="product_img">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" style="border: none;" class="btn btn-secondary" onclick="confirmDelete('<?= $row['product_id'] ?>')">Remove Product</button>
                                    <button type="submit" name="update" style="background-color:#FF7EBC; color: white; border: none;" class="btn btn-primary">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No products found.</p>
            <?php endif; ?>
        </div>




    <?php if (isset($_SESSION['error'])): ?>
        <div class="modal fade" id="errorModal" tabindex="-1">
             <div class="modal-dialog">
                <div class="modal-content bg-danger text-white">
                    <div class="modal-header">
                        <h5 class="modal-title">Error</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <?= $_SESSION['error'] ?>
                    </div>
                </div>
            </div>
        </div>
    <?php unset($_SESSION['error']); endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="modal fade" id="successModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content bg-success text-white">
                    <div class="modal-header">
                        <h5 class="modal-title">Success</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <?= $_SESSION['success'] ?>
                    </div>
                </div>
            </div>
        </div>
    <?php unset($_SESSION['success']); endif; ?>
       


    <!-- Add Product Modal -->
    <div class="modal fade" id="addproduct" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addProductLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered custom-modal">
            <div class="modal-content">
                <form action="../product/create-products.php" method="POST" enctype="multipart/form-data" id="addProductForm">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="addProductLabel">Add New Product</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Product Name -->
                        <div class="mb-3">
                            <label>Product Name</label>
                            <input type="text" name="product_name" class="form-control" required />
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label>Category</label>
                            <select name="category_id" class="form-control" required>
                                <option value="" disabled selected>Select a Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['category_id']; ?>"><?= htmlspecialchars($category['category_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Price -->
                        <div class="mb-3">
                            <label>Product Price</label>
                            <input type="number" name="product_price" class="form-control" step="0.01" required />
                        </div>

                        <!-- Product Description -->
                        <div class="mb-3">
                            <label>Product Description</label>
                            <textarea name="product_description" class="form-control" rows="3" required></textarea>
                        </div>

                        <!-- Estimated Delivery -->
                        <div class="mb-3">
                            <label>Estimated Delivery</label>
                            <input type="text" name="estimated_delivery" class="form-control" placeholder="(e.g. 3 - 5 days)" required />
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label>Status</label>
                            <select name="product_status" class="form-control" required>
                                <option value="Available">Available</option>
                                <option value="Not Available">Not Available</option>
                            </select>
                        </div>

                        <!-- Image Upload -->
                        <div class="mb-3">
                            <label>Product Image</label>
                            <input type="file" name="product_img" class="form-control" accept="image/*" required />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" style="background-color:#FF7EBC; color: white; border: none;" class="btn btn-success">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <!-- CATEGORY MODALS - placed outside the sidebar -->

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered custom-modal">
            <div class="modal-content">
                <form action="../category/add-category.php" method="POST">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="addCategoryModalLabel">Add New Category</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="text" name="category_name" class="form-control" placeholder="Enter new category" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" style="border: none;" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" style="background-color:#FF7EBC; color: white; border: none;" class="btn btn-success">Add Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered custom-modal">
            <div class="modal-content">
               <form action="../category/edit-category.php" method="POST" onsubmit="return confirmCategoryEdit()">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="editCategoryModalLabel">Edit Category</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Select a Category to Edit: </label>
                            <select name="category_id" class="form-control" required>
                                <option value="" disabled selected>Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['category_id']; ?>"><?= htmlspecialchars($category['category_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <input type="text" name="new_category_name" class="form-control" placeholder="Enter new category name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" style="border: none;" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" style="background-color:#FF7EBC; color: white; border: none;"  class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Category Modal -->
    <div class="modal fade" id="deleteCategoryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered custom-modal">
            <div class="modal-content">
                <form action="../category/delete-category.php" method="POST" onsubmit="return confirmCategoryDelete()">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="deleteCategoryModalLabel">Delete Category</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Select Category to Delete</label>
                            <select name="category_id" class="form-control" required>
                                <option value="" disabled selected>Select a Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['category_id']; ?>"><?= htmlspecialchars($category['category_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    $('#addproduct').on('shown.bs.modal', function () {
        $('#addProductForm')[0].reset();
    });

    function confirmDelete(productId) {
        if (confirm('Are you sure you want to delete this product?')) {
            // Redirect to delete-product.php with the product_id
            window.location.href = '../product/delete-product.php?id=' + productId;
        }
    }
</script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../javascript/admin-modals.js"></script>
    <script src="../javascript/admin-confirm.js"></script>
</body>
</html>
