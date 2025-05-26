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
    } elseif (isset($_POST['delete_category'])) {
        // Delete category
        $id = $_POST['category_id'];
        
        try {
            // First check if category has products
            $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($productCount);
            $stmt->fetch();
            
            if ($productCount > 0) {
                $_SESSION['message'] = "Cannot delete category with products. Please reassign or delete products first.";
                $_SESSION['message_type'] = "error";
            } else {
                $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $_SESSION['message'] = "Category deleted successfully!";
                $_SESSION['message_type'] = "success";
            }
            header("Location: ProductCategories.php");
            exit;
        } catch (mysqli_sql_exception $e) {
            $_SESSION['message'] = "Error deleting category: " . $e->getMessage();
            $_SESSION['message_type'] = "error";
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
        /* Base Styles */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f3f4f6;
            color: #222;
            line-height: 1.5;
        }
        
        /* Dashboard Layout */
        .dashboard-container {
            display: flex;
            min-height: 100vh;
            background: #f3f4f6;
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
        
        /* Utility Classes */
        .bg-white { background: #fff; }
        .shadow-sm { box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .rounded-lg { border-radius: 0.5rem; }
        .p-4 { padding: 1rem; }
        .p-6 { padding: 1.5rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mt-4 { margin-top: 1rem; }
        .mr-2 { margin-right: 0.5rem; }
        .ml-3 { margin-left: 0.75rem; }
        .text-lg { font-size: 1.125rem; }
        .font-semibold { font-weight: 600; }
        .text-gray-600 { color: #4b5563; }
        .text-gray-700 { color: #374151; }
        .text-blue-500 { color: #3b82f6; }
        .text-blue-600 { color: #2563eb; }
        .text-red-600 { color: #dc2626; }
        .hover\:text-blue-900:hover { color: #1e40af; }
        .hover\:text-red-900:hover { color: #991b1b; }
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .grid { display: grid; }
        .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
        .gap-6 { gap: 1.5rem; }
        .hidden { display: none; }
        .w-full { width: 100%; }
        .overflow-x-auto { overflow-x: auto; }
        .divide-y > :not([hidden]) ~ :not([hidden]) { border-top-width: 1px; }
        .divide-gray-200 > :not([hidden]) ~ :not([hidden]) { border-color: #e5e7eb; }
        
        /* Table Styles */
        .min-w-full { min-width: 100%; }
        .table-auto { table-layout: auto; }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
        }
        td {
            font-size: 0.875rem;
            color: #374151;
        }
        .whitespace-nowrap { white-space: nowrap; }
        
        /* Form Elements */
        input[type="text"] {
            display: block;
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        input[type="text"]:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            line-height: 1.25rem;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
        }
        
        .btn-primary {
            background-color: #3b82f6;
            color: white;
            border: 1px solid transparent;
        }
        
        .btn-primary:hover {
            background-color: #2563eb;
        }
        
        .btn-danger {
            background-color: #dc2626;
            color: white;
            border: 1px solid transparent;
        }
        
        .btn-danger:hover {
            background-color: #b91c1c;
        }
        
        .btn-secondary {
            background-color: white;
            color: #374151;
            border: 1px solid #d1d5db;
        }
        
        .btn-secondary:hover {
            background-color: #f9fafb;
        }
        
        /* Modal Styles */
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
        }
        
        .modal-container {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 32rem;
            margin: 1rem;
            overflow: hidden;
        }
        
        .modal-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }
        
        /* Icons */
        .icon {
            width: 1.25rem;
            height: 1.25rem;
            flex-shrink: 0;
        }
        
        .icon-sm {
            width: 1rem;
            height: 1rem;
        }
        
        /* Alert Messages */
        .alert {
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
        }
        
        .alert-success {
            color: #166534;
            background-color: #dcfce7;
            border-color: #bbf7d0;
        }
        
        .alert-error {
            color: #991b1b;
            background-color: #fee2e2;
            border-color: #fecaca;
        }
        
        /* Search Input */
        .search-container {
            position: relative;
            width: 100%;
            max-width: 24rem;
        }
        
        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }
        
        .search-input {
            padding-left: 2.25rem;
            width: 100%;
        }
        
        /* Responsive Adjustments */
        @media (min-width: 768px) {
            .md\:flex-row {
                flex-direction: row;
            }
            
            .md\:items-center {
                align-items: center;
            }
            
            .md\:space-x-4 > :not([hidden]) ~ :not([hidden]) {
                margin-left: 1rem;
            }
        }
    </style>
</head>
<body class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'header.php'; ?>
        
        <main class="content-area">
            <!-- Display messages -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?= $_SESSION['message_type'] ?> mb-6">
                    <?= $_SESSION['message'] ?>
                </div>
                <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
            <?php endif; ?>

            <div class="grid grid-cols-1 gap-6 mb-6">
                <div class="bg-white p-4 shadow-sm rounded-lg">
                    <h2 class="text-lg font-semibold mb-4">
                        Product Categories Management
                    </h2>
                    <p class="text-gray-600">
                        Manage product categories to organize your inventory efficiently.
                    </p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <div class="flex items-center mb-4 md:mb-0">
                        <span class="icon text-blue-500 mr-2">
                            <?= getIconSvg('tag') ?>
                        </span>
                        <h2 class="text-lg font-semibold">Categories</h2>
                    </div>
                    <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
                        <div class="search-container">
                            <span class="search-icon icon">
                                <?= getIconSvg('search') ?>
                            </span>
                            <input type="text" id="searchInput" class="search-input" placeholder="Search categories...">
                        </div>
                        <button onclick="openModal('add')" class="btn btn-primary">
                            <span class="icon icon-sm mr-2">
                                <?= getIconSvg('plus') ?>
                            </span>
                            Add Category
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Products</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="categoriesTable">
                            <?php foreach ($categories as $category): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($category['category_name']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= $category['product_count'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= date('Y-m-d', strtotime($category['created_at'])) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button onclick="openModal('edit', <?= htmlspecialchars(json_encode($category)) ?>)" class="text-blue-600 hover:text-blue-900 mr-4">
                                        <span class="icon icon-sm">
                                            <?= getIconSvg('edit') ?>
                                        </span>
                                    </button>
                                    <button onclick="confirmDelete(<?= $category['category_id'] ?>)" class="text-red-600 hover:text-red-900">
                                        <span class="icon icon-sm">
                                            <?= getIconSvg('trash-2') ?>
                                        </span>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Category Modal -->
            <div id="categoryModal" class="hidden">
                <div class="modal-overlay" onclick="closeModal()">
                    <div class="modal-container" onclick="event.stopPropagation()">
                        <form id="categoryForm" method="POST" action="ProductCategories.php">
                            <input type="hidden" name="category_id" id="formCategoryId">
                            <div class="modal-header">
                                <div class="flex items-center justify-center rounded-full bg-blue-100 p-2 mr-4">
                                    <span class="icon text-blue-600">
                                        <?= getIconSvg('tag') ?>
                                    </span>
                                </div>
                                <h3 id="modalTitle" class="text-lg font-semibold"></h3>
                            </div>
                            <div class="modal-body">
                                <div class="space-y-4">
                                    <div>
                                        <label for="categoryName" class="block text-sm font-medium text-gray-700 mb-1">Category Name *</label>
                                        <input type="text" name="category_name" id="categoryName" required class="w-full" placeholder="Enter category name">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="closeModal()" class="btn btn-secondary">
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
                    <div class="modal-container" onclick="event.stopPropagation()">
                        <form id="deleteForm" method="POST" action="ProductCategories.php">
                            <input type="hidden" name="category_id" id="deleteCategoryId">
                            <input type="hidden" name="delete_category" value="1">
                            <div class="modal-header">
                                <div class="flex items-center justify-center rounded-full bg-red-100 p-2 mr-4">
                                    <span class="icon text-red-600">
                                        <?= getIconSvg('alert-triangle') ?>
                                    </span>
                                </div>
                                <h3 class="text-lg font-semibold">Delete Category</h3>
                            </div>
                            <div class="modal-body">
                                <p class="text-gray-700">Are you sure you want to delete this category? This action cannot be undone.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="closeDeleteModal()" class="btn btn-secondary">
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
        const categoryForm = document.getElementById('categoryForm');
        const formCategoryId = document.getElementById('formCategoryId');
        const categoryName = document.getElementById('categoryName');
        
        if (mode === 'add') {
            modalTitle.textContent = 'Add New Category';
            saveButton.textContent = 'Add';
            saveButton.name = 'add_category';
            categoryForm.reset();
            formCategoryId.value = '';
        } else if (mode === 'edit' && category) {
            modalTitle.textContent = 'Edit Category';
            saveButton.textContent = 'Save';
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
            alert('Category name is required');
            document.getElementById('categoryName').focus();
        }
    });
    </script>

    <?php
    function getIconSvg($iconName) {
        $icons = [
            'tag' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>',
            'search' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>',
            'plus' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>',
            'edit' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>',
            'trash-2' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>',
            'alert-triangle' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>'
        ];
        
        return $icons[$iconName] ?? '';
    }
    ?>
</body>
</html>