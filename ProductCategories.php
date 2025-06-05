<?php
// categories.php
$title = "Product Categories";
session_start();

// Database configuration
require 'config.php';

// Check if user is logged in
/* if (!isset($_SESSION['isAuthenticated'])) {
    header('Location: login.php');
    exit;
} */

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category'])) {
        // Add new category
        $name = trim($_POST['category_name']);
        
        try {
            $currentTimestamp = date('Y-m-d H:i:s'); // Get current timestamp
            $stmt = $conn->prepare("INSERT INTO categories (category_name, created_at) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $currentTimestamp);
            $stmt->execute();
            $_SESSION['message'] = "Category added successfully!";
            $_SESSION['message_type'] = "success";
            header("Location: ProductCategories.php");
            exit;
        } catch (mysqli_sql_exception $e) {
            $_SESSION['message'] = "Error adding category: " . $e->getMessage();
            $_SESSION['message_type'] = "error";
        }
    } elseif (isset($_POST['edit_category'])) {
        // Update existing category
        $id = $_POST['category_id'];
        $name = trim($_POST['category_name']);
        
        try {
            $stmt = $conn->prepare("UPDATE categories SET category_name = ? WHERE category_id = ?");
            $stmt->bind_param("si", $name, $id);
            $stmt->execute();
            $_SESSION['message'] = "Category updated successfully!";
            $_SESSION['message_type'] = "success";
            header("Location: ProductCategories.php");
            exit;
        } catch (mysqli_sql_exception $e) {
            $_SESSION['message'] = "Error updating category: " . $e->getMessage();
            $_SESSION['message_type'] = "error";
        }
    } if (isset($_POST['delete_category'])) {
    $id = $_POST['category_id'];

    try {
        // Start transaction
        $conn->begin_transaction();

        // First delete all products in this category
        $deleteProductsStmt = $conn->prepare("DELETE FROM products WHERE category_id = ?");
        $deleteProductsStmt->bind_param("i", $id);
        $deleteProductsStmt->execute();
        $deleteProductsStmt->close();

        // Then delete the category
        $deleteCategoryStmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
        $deleteCategoryStmt->bind_param("i", $id);
        $deleteCategoryStmt->execute();
        $affectedRows = $deleteCategoryStmt->affected_rows;
        $deleteCategoryStmt->close();

        if ($affectedRows > 0) {
            $_SESSION['message'] = "Category and all associated products deleted successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Category not found or already deleted.";
            $_SESSION['message_type'] = "error";
        }

        // Commit transaction if all queries succeeded
        $conn->commit();
    } catch (mysqli_sql_exception $e) {
        // Rollback transaction if any error occurs
        $conn->rollback();
        $_SESSION['message'] = "Error deleting category and products: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    } finally {
        header("Location: ProductCategories.php");
        exit;
    }
}
}

// Fetch all categories with product counts
try {
    $stmt = $conn->prepare("
        SELECT c.category_id, c.category_name, c.created_at, 
               COUNT(p.product_id) as product_count
        FROM categories c
        LEFT JOIN products p ON c.category_id = p.category_id
        GROUP BY c.category_id
        ORDER BY c.category_name
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    $categories = $result->fetch_all(MYSQLI_ASSOC);
    $result->free(); // Free the result set
} catch (mysqli_sql_exception $e) {
    $categories = [];
    $_SESSION['message'] = "Error fetching categories: " . $e->getMessage();
    $_SESSION['message_type'] = "error";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?> - JO TECH</title>
    <style>
        :root {
            --primary: #3b82f6;
            --primary-light: #93c5fd;
            --primary-dark: #1d4ed8;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --light: #f8fafc;
            --dark: #1e293b;
            --gray: #64748b;
            --light-gray: #e2e8f0;
        }

        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f1f5f9;
            color: #334155;
            line-height: 1.5;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .content-area {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .card-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--dark);
        }

        /* Tables */
        .table-container {
            border-radius: 0.5rem;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.75rem 1.25rem;
            text-align: left;
        }

        td {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.875rem;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background-color: #f8fafc;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
            height: fit-content;
            width: fit-content;
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-outline {
            border: 1px solid #e2e8f0;
            background-color: white;
        }

        .btn-outline:hover {
            background-color: #f8fafc;
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        /* Forms */
        .form-control {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        /* Alerts */
        .alert {
            padding: 0.875rem 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-left: 3px solid transparent;
        }

        .alert-success {
            background-color: #ecfdf5;
            color: #059669;
            border-left-color: #059669;
        }

        .alert-error {
            background-color: #fef2f2;
            color: #dc2626;
            border-left-color: #dc2626;
        }

        /* Search */
        .search-container {
            position: relative;
            max-width: 24rem;
            width: 100%;
        }

        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .search-input {
            padding-left: 2.25rem;
            width: 100%;
        }

        /* Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 50;
            padding: 1rem;
        }

        .modal-content {
            background-color: white;
            border-radius: 0.5rem;
            width: 100%;
            max-width: 32rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1.25rem 1.5rem;
            border-top: 1px solid #f1f5f9;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        /* Icons */
        .icon {
            width: 1.25rem;
            height: 1.25rem;
            stroke-width: 2;
        }

        .icon-sm {
            width: 1rem;
            height: 1rem;
        }

        /* Utility classes */
        .flex {
            display: flex;
        }

        .items-center {
            align-items: center;
        }

        .justify-between {
            justify-content: space-between;
        }

        .gap-2 {
            gap: 0.5rem;
        }

        .gap-3 {
            gap: 0.75rem;
        }

        .gap-4 {
            gap: 1rem;
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        .mb-6 {
            margin-bottom: 1.5rem;
        }

        .mr-2 {
            margin-right: 0.5rem;
        }

        .p-4 {
            padding: 1rem;
        }

        .p-6 {
            padding: 1.5rem;
        }

        .text-lg {
            font-size: 1.125rem;
        }

        .text-xl {
            font-size: 1.25rem;
        }

        .font-semibold {
            font-weight: 600;
        }

        .text-gray-600 {
            color: var(--gray);
        }

        .hidden {
            display: none;
        }

        .whitespace-nowrap {
            white-space: nowrap;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .content-area {
                padding: 1rem;
            }
            
            .flex-col-mobile {
                flex-direction: column;
            }
            
            .gap-mobile-3 {
                gap: 0.75rem;
            }
            
            .search-container {
                max-width: 100%;
            }
        }
        .text-blue-600{
            color: #3b82f6;
        }
        .text-blue-600:hover{
            color: #2563eb;
            cursor: pointer;
        }
            .text-red-600{
            color: #ef4444;
            }
            .text-red-600:hover{
            color: #dc2626;
            cursor: pointer;
            }
    </style>
</head>
<body class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'header.php'; ?>
        
        <main class="content-area">
            <!-- Messages -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?= $_SESSION['message_type'] === 'success' ? 'success' : 'error' ?>">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <?php if ($_SESSION['message_type'] === 'success'): ?>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        <?php else: ?>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        <?php endif; ?>
                    </svg>
                    <span><?= $_SESSION['message'] ?></span>
                </div>
                <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
            <?php endif; ?>

            <!-- Header Card -->
            <div class="card mb-6">
                <div class="card-header">
                    <h2 class="card-title">Product Categories Management</h2>
                    <p class="text-gray-600 mt-1">Manage product categories to organize your inventory efficiently.</p>
                </div>
            </div>

            <!-- Main Content Card -->
            <div class="card">
                <div class="card-header flex flex-col-mobile justify-between items-start gap-mobile-3">
                    <div class="flex items-center">
                        <svg class="icon text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold">Categories</h3>
                    </div>
                    <div class="flex flex-col-mobile gap-3 w-full md:w-auto">
                        <div class="search-container">
                            <svg class="search-icon icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" id="searchInput" class="form-control search-input" placeholder="Search categories...">
                        </div>
                        <button onclick="openModal('add')" class="btn btn-primary">
                            <svg class="icon-sm mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Category
                        </button>
                    </div>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Category Name</th>
                                <th>Products</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="categoriesTable">
                            <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?= htmlspecialchars($category['category_name']) ?></td>
                                <td><?= $category['product_count'] ?></td>
                                <td><?= date('M j, Y', strtotime($category['created_at'])) ?></td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <button onclick="openModal('edit', <?= htmlspecialchars(json_encode($category)) ?>)" class="text-blue-600 hover:text-blue-800">
                                            <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button onclick="confirmDelete(<?= $category['category_id'] ?>)" class="text-red-600 hover:text-red-800">
                                            <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add/Edit Category Modal -->
            <div id="categoryModal" class="hidden">
                <div class="modal-overlay" onclick="closeModal()">
                    <div class="modal-content" onclick="event.stopPropagation()">
                        <form id="categoryForm" method="POST" action="ProductCategories.php">
                            <input type="hidden" name="category_id" id="formCategoryId">
                            <div class="modal-header">
                                <div class="flex items-center justify-center rounded-full bg-blue-100 p-2 mr-4">
                                    <svg class="icon text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                                <h3 id="modalTitle" class="text-xl font-semibold"></h3>
                            </div>
                            <div class="modal-body">
                                <div class="space-y-4">
                                    <div>
                                        <label for="categoryName" class="form-label">Category Name *</label>
                                        <input type="text" name="category_name" id="categoryName" class="form-control" required placeholder="Enter category name">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="closeModal()" class="btn btn-outline">
                                    Cancel
                                </button>
                                <button type="submit" id="saveButton" name="add_category" class="btn btn-primary">
                                    Add
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div id="deleteModal" class="hidden">
                <div class="modal-overlay">
                    <div class="modal-content" onclick="event.stopPropagation()">
                        <form id="deleteForm" method="POST" action="ProductCategories.php">
                            <input type="hidden" name="category_id" id="deleteCategoryId">
                            <input type="hidden" name="delete_category" value="1">
                            <div class="modal-header">
                                <div class="flex items-center justify-center rounded-full bg-red-100 p-2 mr-4">
                                    <svg class="icon text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-semibold">Delete Category</h3>
                            </div>
                            <div class="modal-body">
                                <p class="text-gray-600">Are you sure you want to delete this category? This action cannot be undone.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="closeDeleteModal()" class="btn btn-outline">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-danger">
                                    Delete
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
    // Modal functions
    function openModal(mode, category = null) {
        const modal = document.getElementById('categoryModal');
        const modalTitle = document.getElementById('modalTitle');
        const saveButton = document.getElementById('saveButton');
        const formCategoryId = document.getElementById('formCategoryId');
        const categoryName = document.getElementById('categoryName');
        
        if (mode === 'add') {
            modalTitle.textContent = 'Add New Category';
            saveButton.textContent = 'Add';
            saveButton.name = 'add_category';
            document.getElementById('categoryForm').reset();
            formCategoryId.value = '';
        } else if (mode === 'edit' && category) {
            modalTitle.textContent = 'Edit Category';
            saveButton.textContent = 'Save Changes';
            saveButton.name = 'edit_category';
            formCategoryId.value = category.category_id;
            categoryName.value = category.category_name;
        }
        
        modal.classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('categoryModal').classList.add('hidden');
    }

    function confirmDelete(categoryId) {
        document.getElementById('deleteCategoryId').value = categoryId;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('#categoriesTable tr');
        
        rows.forEach(row => {
            const name = row.cells[0].textContent.toLowerCase();
            if (name.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Form validation
    document.getElementById('categoryForm').addEventListener('submit', function(e) {
        const name = document.getElementById('categoryName').value.trim();
        if (!name) {
            e.preventDefault();
            alert('Please enter a category name');
            document.getElementById('categoryName').focus();
        }
    });
    </script>
</body>
</html>