<?php
session_start();
require_once '../database/db.php';

// Fetch categories from the database
$sql_categories = "SELECT * FROM category_tbl ORDER BY category_name ASC";
$stmt_categories = $conn->prepare($sql_categories);

if ($stmt_categories->execute()) {
    $categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);
} else {
    $categories = [];
    echo "Error fetching categories: " . $stmt_categories->errorInfo()[2];
}

// Ensure 'Others' category is included
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

// Sort categories alphabetically, ensuring 'Others' is last
usort($categories, function($a, $b) {
    if ($a['category_name'] === 'Others') return 1;
    if ($b['category_name'] === 'Others') return -1;
    return strcmp($a['category_name'], $b['category_name']);
});

// Get filters
$category_filter = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;
$status_filter = isset($_GET['status']) ? htmlspecialchars($_GET['status']) : null;

// Validate product status
$valid_statuses = ['Available', 'Not Available'];
if ($status_filter && !in_array($status_filter, $valid_statuses)) {
    $status_filter = null;
}

// Build SQL query
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

if (count($where_conditions) > 0) {
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Adorable Knots</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Afacad:ital,wght@0,400..700;1,400..700&family=Caveat:wght@400..700&family=Dosis:wght@200..800&display=swap" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/navbar.css"/>
    <link rel="stylesheet" href="../css/admin-page.css"/>
</head>
<body>

    <nav class="custom-navbar">
        <div class="custom-navbar-header">Admin Page</div>
        <div class="custom-navbar-contents">
            <div class="logo">
            <img src="../assets/web_img/ak-logo.png?v2" alt="Adorable Knots Logo">
            </div>

            <div class="nav-links">
                <button class="active"><a href="admin-page.php">Products</a></button>
                <button><a href="store-page.php">Orders</a></button>
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
                <?php $category_query = isset($_GET['category_id']) ? '&category_id=' . urlencode($_GET['category_id']) : ''; ?>
                <?php
                $all_status_href = 'admin-page.php';
                if (isset($_GET['category_id'])) {
                    $all_status_href .= '?category_id=' . urlencode($_GET['category_id']);
                }
                ?>
                <button class="nav-button <?= !isset($_GET['status']) ? 'active' : '' ?>" onclick="location.href='<?= $all_status_href ?>'">All Status</button>

                <button class="nav-button <?= (isset($_GET['status']) && $_GET['status'] === 'Available') ? 'active' : '' ?>" onclick="location.href='admin-page.php?status=Available<?= $category_query ?>'">
                Available
                </button>

                <button class="nav-button <?= (isset($_GET['status']) && $_GET['status'] === 'Not Available') ? 'active' : '' ?>" onclick="location.href='admin-page.php?status=Not Available<?= $category_query ?>'">
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
            <button type="button" class="btn" style="background-color:#8c8c8c; color: white" data-bs-toggle="modal" data-bs-target="#addproduct">ADD NEW PRODUCT</button>
        </div>

        <div class="product-grid">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $row): ?>
                    <!-- Product Card -->
                    <div class="product-card" data-bs-toggle="modal" data-bs-target="#editProductModal<?= $row['product_id'] ?>">
                        <img src="../uploads/<?= htmlspecialchars($row['product_img']) ?>" alt="<?= htmlspecialchars($row['product_name']) ?>" class="product-image">
                        <div class="card-body">
                            <div class="product-name"><?= htmlspecialchars($row['product_name']) ?></div>
                            <p class="product-price"><strong>₱<?= number_format($row['product_price'], 2) ?></strong></p>
                            <span class="status <?= $row['product_status'] === 'Available' ? 'available' : 'not-available' ?>">
                                <?= $row['product_status'] ?>
                            </span>
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

                                    <!-- Product Customizations -->
                                    <div class="mb-3">
                                        <label>Product Customizations</label>
                                        <div id="customizationFields<?= $row['product_id'] ?>" class="customization-fields">
                                            <!-- Dynamically populate existing customizations -->
                                            <?php
                                            // Check if customizations exist before attempting to decode it
                                            $customizations = isset($row['customizations']) ? json_decode($row['customizations'], true) : null;
                                            if ($customizations) {
                                                foreach ($customizations as $type => $options) {
                                                    foreach ($options as $option) {
                                                        echo '<div class="input-group mb-2">
                                                                <input type="text" class="form-control me-1" placeholder="Type (e.g., Color)" name="customization_type[]" value="' . htmlspecialchars($type) . '">
                                                                <input type="text" class="form-control me-1" placeholder="Options (comma-separated)" name="customization_options[]" value="' . htmlspecialchars(implode(',', $options)) . '">
                                                                <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()">✕</button>
                                                            </div>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </div>
                                        <button type="button" style="background-color:#8c8c8c; color: white; border: none; " class="btn btn-sm btn-secondary mt-2" onclick="addCustomizationField(<?= $row['product_id'] ?>)">Add Customization</button>
                                        <input type="hidden" name="customizations" id="customizationsJson<?= $row['product_id'] ?>">
                                    </div>

                                    <!-- Price -->
                                    <div class="mb-3">
                                        <label>Price</label>
                                        <input type="number" class="form-control" name="product_price" value="<?= $row['product_price'] ?>" required>
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
                                    <button type="submit" name="update" style="background-color:#FF7EBC; color: white; border: none;" class="btn btn-primary">Save changes</button>
                                    <button type="button" style="background-color:#8c8c8c; color: white; border: none;" class="btn btn-danger" onclick="confirmDelete(<?= $row['product_id'] ?>)">Remove Product</button>
                                </div>
                            </form>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <p>No products found.</p>
            <?php endif; ?>
        </div>
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
                <form action="../product/create-products.php" method="POST" enctype="multipart/form-data">
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

                        <!-- Customizations -->
                        <div class="mb-3">
                            <label>Product Customizations</label>
                            <div id="customizationFields" class="customization-fields">
                                <!-- Dynamic fields will be added here -->
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary mt-2" onclick="addCustomizationField('add')">Add Customization</button>
                            <input type="hidden" name="customizations" id="customizationsJson">
                        </div>

                        <!-- Price -->
                        <div class="mb-3">
                            <label>Product Price</label>
                            <input type="number" name="product_price" class="form-control" step="0.01" required />
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
                        <button type="submit" class="btn btn-success">Add Product</button>
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Category</button>
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
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
        // Function to add a new customization field dynamically for a given modal
        function addCustomizationField(productId) {
            const suffix = productId === 'add' ? '' : productId;
            const wrapper = document.getElementById("customizationFields" + suffix);

            const div = document.createElement("div");
            div.classList.add("mb-2");

            div.innerHTML = `
                <div class="input-group">
                    <input type="text" class="form-control me-1" placeholder="Type (e.g., Color)" name="customization_type_${suffix}[]">
                    <input type="text" class="form-control me-1" placeholder="Options (comma-separated)" name="customization_options_${suffix}[]">
                    <button type="button" class="btn btn-danger" onclick="this.parentElement.parentElement.remove()">✕</button>
                </div>
            `;

            wrapper.appendChild(div);
        }

        // Function to handle form submission and convert the customization fields to JSON format
        function buildCustomizationJSON(productId) {
            const suffix = productId === 'add' ? '' : productId;
            const types = document.querySelectorAll(`input[name="customization_type_${suffix}[]"]`);
            const options = document.querySelectorAll(`input[name="customization_options_${suffix}[]"]`);
            let json = {};

            types.forEach((typeInput, index) => {
                const key = typeInput.value.trim();
                const value = options[index].value.split(',').map(opt => opt.trim()).filter(opt => opt);
                if (key && value.length > 0) json[key] = value;
            });

            document.getElementById('customizationsJson' + suffix).value = JSON.stringify(json);
        }

        // Listen for all form submissions (edit or add)
        document.addEventListener("submit", function(e) {
            if (e.target && e.target.matches("form")) {
                const productIdInput = e.target.querySelector("input[name='product_id']");
                const productId = productIdInput ? productIdInput.value : 'add';
                buildCustomizationJSON(productId);
            }
        });
    </script>

                      

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../javascript/admin-modals.js"></script>
    <script src="../javascript/admin-confirm.js"></script>
</body>
</html>
