function confirmEdit() {
    return confirm("Are you sure you want to save the changes?");
}

function confirmDelete(productId) {
    if (confirm("Are you sure you want to delete this product?")) {
        window.location.href = "../product/delete-product.php?id=" + productId;
    }
}

function confirmCategoryEdit() {
    return confirm("Are you sure you want to update this category?");
}

function confirmCategoryDelete() {
    return confirm("Are you sure you want to delete this category?");
}
