<?php
// StockManagement.php
$title = "Stock Management";
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config.php'; // Your MySQLi connection file

$user_id = $_SESSION['user_id'];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'add_product') {
        // Add new product
        $name = $conn->real_escape_string($_POST['product_name']);
        $category_id = (int)$_POST['category_id'];
        $quantity = (int)$_POST['quantity'];
        $min_required = (int)$_POST['min_required'];
        $price = floatval($_POST['price']);
        
        // Prepare the SQL statement
        $sql = "INSERT INTO products (product_name, category_id, quantity, minimum_stock, price, created_at) 
                VALUES ('$name', $category_id, $quantity, $min_required, $price, NOW())";

        // Debugging: Print the SQL query
        error_log("SQL Query: $sql");

        // Execute the query
        if ($conn->query($sql)) {
            $_SESSION['success'] = "Product added successfully!";
        } else {
            // Enhanced error message
            $_SESSION['error'] = "Error adding product: " . $conn->error;
            error_log("MySQL Error: " . $conn->error); // Log the error
        }
        header("Location: StockManagement.php");
        exit;
    }
    if ($_POST['action'] === 'update_product') {
        // Update product
        $product_id = (int)$_POST['product_id'];
        $name = $conn->real_escape_string($_POST['product_name']);
        $category_id = (int)$_POST['category_id'];
        $min_required = (int)$_POST['min_required'];
        $price = floatval($_POST['price']);
        
        $sql = "UPDATE products SET 
                product_name = '$name', 
                category_id = $category_id, 
                minimum_stock = $min_required,
                price = $price
                WHERE product_id = $product_id";
        
        if ($conn->query($sql)) {
            $_SESSION['success'] = "Product updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating product: " . $conn->error;
        }
        header("Location: StockManagement.php");
        exit;
    }
    elseif (isset($_POST['adjust_stock'])) {
        // Adjust stock
        $product_id = (int)$_POST['product_id'];
        $adjustment = (int)$_POST['adjustment'];
        $update_type = $_POST['adjustment_type'] === 'add' ? 'increment' : 'decrement';
        
        // First get current quantity
        $result = $conn->query("SELECT quantity FROM products WHERE product_id = $product_id");
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $current_quantity = $row['quantity'];
            $new_quantity = $update_type === 'increment' ? 
                $current_quantity + $adjustment : 
                $current_quantity - $adjustment;
            
            if ($new_quantity < 0) {
                $_SESSION['error'] = "Stock cannot be negative!";
            } else {
                // Update product quantity
                $conn->query("UPDATE products SET quantity = $new_quantity WHERE product_id = $product_id");
                
                // Record in stock_update
                $conn->query("INSERT INTO stock_update (update_type, product_id, quantity, user_id, created_at)
                             VALUES ('$update_type', $product_id, $adjustment, $user_id, NOW())");
                
                $_SESSION['success'] = "Stock adjusted successfully!";
            }
        } else {
            $_SESSION['error'] = "Product not found!";
        }
        header("Location: StockManagement.php");
        exit;
    }
    elseif (isset($_POST['delete_product'])) {
        // Delete product
        $product_id = (int)$_POST['product_id'];
        
        // First check if product exists
        $result = $conn->query("SELECT product_id FROM products WHERE product_id = $product_id");
        if ($result && $result->num_rows > 0) {
            $conn->query("DELETE FROM products WHERE product_id = $product_id");
            $_SESSION['success'] = "Product deleted successfully!";
        } else {
            $_SESSION['error'] = "Product not found!";
        }
        header("Location: StockManagement.php");
        exit;
    }
}

// Fetch products with category names
$products = [];
$sql = "SELECT p.*, c.category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id
        ORDER BY p.product_name";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Fetch categories for dropdown
$categories = [];
$result = $conn->query("SELECT * FROM categories ORDER BY category_name");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?> - JO TECH</title>
    <style>
          body, html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f3f4f6;
            color: #222;
        }
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
        .bg-white { background: #fff; }
        .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); }
        .rounded-lg { border-radius: 0.5rem; }
        .p-4 { padding: 1rem; }
        .p-6 { padding: 1.5rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .text-lg { font-size: 1.125rem; }
        .font-semibold { font-weight: 600; }
        .text-gray-600 { color: #4b5563; }
        .text-gray-500 { color: #6b7280; }
        .text-blue-500 { color: #3b82f6; }
        .text-blue-600 { color: #2563eb; }
        .text-blue-700 { color: #1d4ed8; }
        .text-red-500 { color: #ef4444; }
        .text-red-600 { color: #dc2626; }
        .text-red-900 { color: #7f1d1d; }
        .text-blue-900 { color: #1e3a8a; }
        .text-white { color: #fff; }
        .text-sm { font-size: 0.875rem; }
        .font-medium { font-weight: 500; }
        .whitespace-nowrap { white-space: nowrap; }
        .px-2 { padding-left: 0.5rem; padding-right: 0.5rem; }
        .px-3 { padding-left: 0.75rem; padding-right: 0.75rem; }
        .px-4 { padding-left: 1rem; padding-right: 1rem; }
        .px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
        .py-1 { padding-top: 0.25rem; padding-bottom: 0.25rem; }
        .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
        .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
        .py-4 { padding-top: 1rem; padding-bottom: 1rem; }
        .rounded { border-radius: 0.25rem; }
        .border { border-width: 1px; border-style: solid; border-color: #d1d5db; }
        .border-gray-300 { border-color: #d1d5db; }
        .border-gray-400 { border-color: #9ca3af; }
        .border-blue-500 { border-color: #3b82f6; }
        .border-green-400 { border-color: #4ade80; }
        .bg-blue-600 { background: #2563eb; }
        .bg-blue-700 { background: #1d4ed8; }
        .bg-blue-50 { background: #eff6ff; }
        .bg-green-100 { background: #d1fae5; }
        .bg-gray-50 { background: #f9fafb; }
        .bg-gray-100 { background: #f3f4f6; }
        .bg-gray-500 { background: #6b7280; }
        .hover\:bg-blue-700:hover { background: #1d4ed8; }
        .hover\:bg-gray-50:hover { background: #f9fafb; }
        .hover\:text-blue-900:hover { color: #1e3a8a; }
        .hover\:text-red-900:hover { color: #7f1d1d; }
        .hover\:text-gray-700:hover { color: #374151; }
        .focus\:outline-none:focus { outline: none; }
        .focus\:ring-1:focus { box-shadow: 0 0 0 1px #3b82f6; }
        .focus\:ring-2:focus { box-shadow: 0 0 0 2px #3b82f6; }
        .focus\:ring-blue-500:focus { box-shadow: 0 0 0 2px #3b82f6; }
        .focus\:border-blue-500:focus { border-color: #3b82f6; }
        .focus\:ring-offset-2:focus { box-shadow: 0 0 0 4px #f3f4f6, 0 0 0 2px #3b82f6; }
        .sm\:text-sm { font-size: 0.875rem; }
        .sm\:w-auto { width: auto; }
        .sm\:ml-3 { margin-left: 0.75rem; }
        .sm\:mt-0 { margin-top: 0; }
        .sm\:p-0 { padding: 0; }
        .sm\:p-6 { padding: 1.5rem; }
        .sm\:pb-4 { padding-bottom: 1rem; }
        .sm\:my-8 { margin-top: 2rem; margin-bottom: 2rem; }
        .sm\:align-middle { vertical-align: middle; }
        .sm\:max-w-lg { max-width: 32rem; }
        .sm\:inline-block { display: inline-block; }
        .sm\:flex { display: flex; }
        .sm\:items-start { align-items: flex-start; }
        .sm\:block { display: block; }
        .sm\:mt-0 { margin-top: 0; }
        .sm\:ml-4 { margin-left: 1rem; }
        .sm\:w-auto { width: auto; }
        .sm\:text-sm { font-size: 0.875rem; }
        .grid { display: grid; }
        .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
        .gap-4 { gap: 1rem; }
        .gap-6 { gap: 1.5rem; }
        .space-x-1 > :not([hidden]) ~ :not([hidden]) { margin-left: 0.25rem; }
        .space-x-2 > :not([hidden]) ~ :not([hidden]) { margin-left: 0.5rem; }
        .space-x-4 > :not([hidden]) ~ :not([hidden]) { margin-left: 1rem; }
        .space-y-4 > :not([hidden]) ~ :not([hidden]) { margin-top: 1rem; }
        .flex { display: flex; }
        .flex-col { flex-direction: column; }
        .flex-row { flex-direction: row; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .justify-center { justify-content: center; }
        .min-w-full { min-width: 100%; }
        .overflow-x-auto { overflow-x: auto; }
        .overflow-y-auto { overflow-y: auto; }
        .overflow-hidden { overflow: hidden; }
        .fixed { position: fixed; }
        .absolute { position: absolute; }
        .inset-0 { top: 0; right: 0; bottom: 0; left: 0; }
        .top-4 { top: 1rem; }
        .right-4 { right: 1rem; }
        .z-10 { z-index: 10; }
        .z-50 { z-index: 50; }
        .hidden { display: none; }
        .block { display: block; }
        .inline-block { display: inline-block; }
        .w-full { width: 100%; }
        .w-12 { width: 3rem; }
        .h-12 { height: 3rem; }
        .h-10 { height: 2.5rem; }
        .w-10 { width: 2.5rem; }
        .h-4 { height: 1rem; }
        .w-4 { width: 1rem; }
        .h-5 { height: 1.25rem; }
        .w-5 { width: 1.25rem; }
        .h-6 { height: 1.5rem; }
        .w-6 { width: 1.5rem; }
        .pl-3 { padding-left: 0.75rem; }
        .pl-7 { padding-left: 1.75rem; }
        .pl-10 { padding-left: 2.5rem; }
        .pr-3 { padding-right: 0.75rem; }
        .pr-10 { padding-right: 2.5rem; }
        .pr-12 { padding-right: 3rem; }
        .mt-1 { margin-top: 0.25rem; }
        .mt-2 { margin-top: 0.5rem; }
        .mt-3 { margin-top: 0.75rem; }
        .mt-4 { margin-top: 1rem; }
        .mx-auto { margin-left: auto; margin-right: auto; }
        .ml-2 { margin-left: 0.5rem; }
        .ml-3 { margin-left: 0.75rem; }
        .ml-4 { margin-left: 1rem; }
        .mr-2 { margin-right: 0.5rem; }
        .mr-1 { margin-right: 0.25rem; }
        .font-medium { font-weight: 500; }
        .leading-5 { line-height: 1.25rem; }
        .leading-6 { line-height: 1.5rem; }
        .rounded-md { border-radius: 0.375rem; }
        .rounded-full { border-radius: 9999px; }
        .pointer-events-none { pointer-events: none; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .align-middle { vertical-align: middle; }
        .min-h-screen { min-height: 100vh; }
        .max-w-lg { max-width: 32rem; }
        .w-auto { width: auto; }
        .w-full { width: 100%; }
        .border-transparent { border-color: transparent; }
        .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); }
        .transition-opacity { transition: opacity 0.2s; }
        .opacity-75 { opacity: 0.75; }
        .z-10 { z-index: 10; }
        .z-50 { z-index: 50; }
        .sm\:block { display: block; }
        .sm\:inline-block { display: inline-block; }
        .sm\:align-middle { vertical-align: middle; }
        .sm\:my-8 { margin-top: 2rem; margin-bottom: 2rem; }
        .sm\:max-w-lg { max-width: 32rem; }
        .sm\:w-auto { width: auto; }
        .sm\:ml-3 { margin-left: 0.75rem; }
        .sm\:mt-0 { margin-top: 0; }
        .sm\:p-0 { padding: 0; }
        .sm\:p-6 { padding: 1.5rem; }
        .sm\:pb-4 { padding-bottom: 1rem; }
        .sm\:flex { display: flex; }
        .sm\:items-start { align-items: flex-start; }
        .sm\:ml-4 { margin-left: 1rem; }
        .sm\:text-sm { font-size: 0.875rem; }
        .sm\:w-auto { width: auto; }
        .sm\:mt-0 { margin-top: 0; }
        .sm\:ml-3 { margin-left: 0.75rem; }
        .sm\:w-auto { width: auto; }
        .sm\:text-sm { font-size: 0.875rem; }
        /* Hide scrollbars for Chrome, Safari and Opera */
        .content-area::-webkit-scrollbar { display: none; }
        /* Hide scrollbars for IE, Edge and Firefox */
        .content-area { -ms-overflow-style: none; scrollbar-width: none; }
        .right-0{right: 0;}
        .top-1{top: 0.5rem;}
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0,0,0,0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .modal-content {
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            transform: translateY(-20px);
            transition: transform 0.3s ease;
        }
        
        .modal-overlay.active .modal-content {
            transform: translateY(0);
        }
        
        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #111827;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6b7280;
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
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }
        
        .form-input {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);
        }
        
        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .btn-primary {
            background-color: #3b82f6;
            color: white;
            border: 1px solid #3b82f6;
        }
        
        .btn-primary:hover {
            background-color: #2563eb;
            border-color: #2563eb;
        }
        
        .btn-secondary {
            background-color: white;
            color: #374151;
            border: 1px solid #d1d5db;
        }
        
        .btn-secondary:hover {
            background-color: #f9fafb;
        }
        
        .btn-danger {
            background-color: #ef4444;
            color: white;
            border: 1px solid #ef4444;
        }
        
        .btn-danger:hover {
            background-color: #dc2626;
            border-color: #dc2626;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        
        .alert-error {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }
    </style>
</head>
<body class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'header.php'; ?>
        
        <main class="content-area">
            <div class="grid grid-cols-1 gap-6 mb-6">
                <div class="bg-white p-4 shadow-sm rounded-lg">
                    <h2 class="text-lg font-semibold mb-4">Stock Management</h2>
                    <p class="text-gray-600">
                        Manage your inventory by adding, editing, or removing products.
                        Track stock levels and set minimum required quantities.
                    </p>
                </div>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <div class="flex items-center mb-4 md:mb-0">
                        <?= getIconSvg('box', 'h-5 w-5 text-blue-500 mr-2') ?>
                        <h2 class="text-lg font-semibold">Products</h2>
                    </div>
                    <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 top-1 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" id="searchInput" class="block w-full py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Search products...">
                        </div>
                        <div class="relative inline-block text-left">
                            <select id="categoryFilter" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['category_id'] ?>"><?= htmlspecialchars($category['category_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button onclick="openModal('add')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <?= getIconSvg('plus', 'h-4 w-4 mr-2') ?>
                            Add Product
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Level</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min Required</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="productsTableBody">
                            <?php foreach ($products as $product): 
                                $stockClass = $product['quantity'] < $product['minimum_stock'] ? 'text-red-500 font-medium' : 'text-gray-500';
                            ?>
                            <tr data-category="<?= $product['category_id'] ?>" data-name="<?= strtolower(htmlspecialchars($product['product_name'])) ?>">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($product['product_name']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($product['category_name']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="mr-2 <?= $stockClass ?>"><?= $product['quantity'] ?></span>
                                        <div class="flex space-x-1">
                                            <button onclick="adjustStock(<?= $product['product_id'] ?>, -1)" class="text-gray-500 hover:text-gray-700">
                                                <?= getIconSvg('arrow-down', 'h-4 w-4') ?>
                                            </button>
                                            <button onclick="adjustStock(<?= $product['product_id'] ?>, 1)" class="text-gray-500 hover:text-gray-700">
                                                <?= getIconSvg('arrow-up', 'h-4 w-4') ?>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $product['minimum_stock'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$<?= number_format($product['price'], 2) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('Y-m-d', strtotime($product['created_at'])) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <button onclick="openAdjustmentModal(<?= htmlspecialchars(json_encode($product)) ?>)" class="text-blue-600 hover:text-blue-900 bg-blue-50 px-2 py-1 rounded">
                                        Adjust Quantity
                                    </button>
                                    <button onclick="openModal('edit', <?= htmlspecialchars(json_encode($product)) ?>)" class="text-blue-600 hover:text-blue-900">
                                        <?= getIconSvg('edit', 'h-4 w-4') ?>
                                    </button>
                                    <button onclick="confirmDelete(<?= $product['product_id'] ?>)" class="text-red-600 hover:text-red-900">
                                        <?= getIconSvg('trash-2', 'h-4 w-4') ?>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="flex items-center justify-between mt-6">
                    <div class="text-sm text-gray-500">
                        Showing <span class="font-medium">1</span> to <span class="font-medium"><?= count($products) ?></span> of <span class="font-medium"><?= count($products) ?></span> results
                    </div>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </button>
                        <button class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Success Message -->
    <div id="successMessage" class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50 hidden"></div>

    <!-- Product Modal -->
  <!-- Product Modal -->
<div id="productModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle" class="modal-title"></h3>
            <button onclick="closeModal()" class="modal-close">&times;</button>
        </div>
        <form id="productForm" method="POST" class="modal-body">
            <input type="hidden" name="product_id" id="formProductId">
            <input type="hidden" name="action" id="formAction" value="add_product">
            
            <div class="form-group">
                <label for="productName" class="form-label">Product Name</label>
                <input type="text" id="productName" name="product_name" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label for="category" class="form-label">Category</label>
                <select id="category" name="category_id" class="form-input" required>
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['category_id'] ?>"><?= htmlspecialchars($category['category_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label for="stock" class="form-label">Initial Stock</label>
                    <input type="number" id="stock" name="quantity" class="form-input" min="0" value="0" required>
                </div>
                <div class="form-group">
                    <label for="minRequired" class="form-label">Min Required</label>
                    <input type="number" id="minRequired" name="min_required" class="form-input" min="0" value="0" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="price" class="form-label">Price</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                    </div>
                    <input type="number" id="price" name="price" step="0.01" min="0" class="form-input pl-7" placeholder="0.00" required>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

    <!-- Quantity Adjustment Modal -->
    <div id="adjustmentModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Adjust Stock Quantity</h3>
                <button onclick="closeAdjustmentModal()" class="modal-close">&times;</button>
            </div>
            <form id="adjustmentForm" method="POST" class="modal-body">
                <input type="hidden" name="product_id" id="adjustProductId">
                <input type="hidden" name="adjust_stock" value="1">
                
                <div class="form-group">
                    <p id="currentStockText" class="text-sm text-gray-500"></p>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Adjustment Type</label>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="adjustment_type" value="add" checked class="mr-2">
                            <span>Add</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="adjustment_type" value="deduct" class="mr-2">
                            <span>Deduct</span>
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="adjustmentQuantity" class="form-label">Quantity to adjust</label>
                    <input type="number" id="adjustmentQuantity" name="adjustment" min="1" value="1" class="form-input" required>
                </div>
                
                <div class="text-sm text-gray-500">
                    New stock will be: <span id="newStockValue" class="font-medium"></span>
                </div>
                
                <div class="modal-footer">
                    <button type="button" onclick="closeAdjustmentModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm Adjustment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Confirm Deletion</h3>
                <button onclick="closeDeleteModal()" class="modal-close">&times;</button>
            </div>
            <form id="deleteForm" method="POST" class="modal-body">
                <input type="hidden" name="product_id" id="deleteProductId">
                <input type="hidden" name="delete_product" value="1">
                
                <p class="text-gray-700">Are you sure you want to delete this product? This action cannot be undone.</p>
                
                <div class="modal-footer">
                    <button type="button" onclick="closeDeleteModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<script>
// Global variables
let currentAdjustingProduct = null;
let currentDeletingProductId = null;

// Product modal functions
function openModal(mode, product = null) {
    const modal = document.getElementById('productModal');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('productForm');
    
    if (mode === 'add') {
        modalTitle.textContent = 'Add New Product';
        form.reset();
        document.getElementById('formProductId').value = '';
        document.getElementById('formAction').value = 'add_product'; // Set action for adding
    } else if (mode === 'edit' && product) {
        modalTitle.textContent = 'Edit Product';
        document.getElementById('formProductId').value = product.product_id;
        document.getElementById('productName').value = product.product_name;
        document.getElementById('category').value = product.category_id;
        document.getElementById('stock').value = product.quantity;
        document.getElementById('minRequired').value = product.minimum_stock;
        document.getElementById('price').value = product.price;
        document.getElementById('formAction').value = 'update_product'; // Set action for updating
    }
    
    modal.classList.add('active');
}

function closeModal() {
    document.getElementById('productModal').classList.remove('active');
}

// Stock adjustment functions
function openAdjustmentModal(product) {
    currentAdjustingProduct = product;
    const modal = document.getElementById('adjustmentModal');
    const currentStockText = document.getElementById('currentStockText');
    
    currentStockText.textContent = `Current stock for ${product.product_name}: ${product.quantity}`;
    document.getElementById('adjustProductId').value = product.product_id;
    document.getElementById('adjustmentQuantity').value = 1;
    document.querySelector('input[name="adjustment_type"][value="add"]').checked = true;
    updateNewStockValue();
    
    modal.classList.add('active');
}

function closeAdjustmentModal() {
    document.getElementById('adjustmentModal').classList.remove('active');
    currentAdjustingProduct = null;
}

function updateNewStockValue() {
    if (!currentAdjustingProduct) return;
    
    const adjustmentType = document.querySelector('input[name="adjustment_type"]:checked').value;
    const quantity = parseInt(document.getElementById('adjustmentQuantity').value) || 0;
    const currentQty = Number(currentAdjustingProduct.quantity) || 0;
    const adjQty = Number(quantity) || 0;
    const newStock = adjustmentType === 'add' 
        ? currentQty + adjQty 
        : currentQty - adjQty;
    
    document.getElementById('newStockValue').textContent = Math.max(0, newStock);
}

// Delete confirmation functions
function confirmDelete(productId) {
    currentDeletingProductId = productId;
    const modal = document.getElementById('deleteModal');
    document.getElementById('deleteProductId').value = productId;
    modal.classList.add('active');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
    currentDeletingProductId = null;
}

// Event listeners for adjustment modal
document.querySelectorAll('input[name="adjustment_type"]').forEach(radio => {
    radio.addEventListener('change', updateNewStockValue);
});

document.getElementById('adjustmentQuantity').addEventListener('input', updateNewStockValue);

// Search and filter functionality
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#productsTableBody tr');
    
    rows.forEach(row => {
        const productName = row.getAttribute('data-name');
        if (productName.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

document.getElementById('categoryFilter').addEventListener('change', function() {
    const categoryId = this.value;
    const rows = document.querySelectorAll('#productsTableBody tr');
    
    rows.forEach(row => {
        const rowCategory = row.getAttribute('data-category');
        if (categoryId === '' || rowCategory === categoryId) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Helper functions
function adjustStock(productId, adjustment) {
    // In a real app, you might want to make an AJAX call for quick adjustments
    // For now, we'll just open the adjustment modal with preset values
    const product = <?= json_encode($products) ?>.find(p => p.product_id == productId);
    if (product) {
        openAdjustmentModal(product);
        document.getElementById('adjustmentQuantity').value = Math.abs(adjustment);
        document.querySelector(`input[name="adjustment_type"][value="${adjustment > 0 ? 'add' : 'deduct'}"]`).checked = true;
        updateNewStockValue();
    }
}

function showSuccessMessage(message) {
    const successMessage = document.getElementById('successMessage');
    successMessage.textContent = message;
    successMessage.classList.remove('hidden');
    
    setTimeout(() => {
        successMessage.classList.add('hidden');
    }, 3000);
}
</script>

<?php
function getIconSvg($iconName, $classes = '') {
    $icons = [
        'box' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>',
        'search' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
        'plus' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>',
        'arrow-down' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><polyline points="19 12 12 19 5 12"></polyline></svg>',
        'arrow-up' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg>',
        'edit' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>',
        'trash-2' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>'
    ];
    
    return $icons[$iconName] ?? '';
}
?>