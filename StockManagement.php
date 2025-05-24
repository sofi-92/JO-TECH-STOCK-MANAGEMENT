<?php
// stock.php
$title = "Stock Management";
session_start();

// Check if user is logged in
if (!isset($_SESSION['isAuthenticated'])) {
    header('Location: login.php');
    exit;
}
$username = $_SESSION['user']['username'] ?? 'User';
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
    </style>
</head>
<body class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <?php 
        // Include header with the current title
        include 'header.php'; 
        ?>
        
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
                <input type="text" class="block w-full py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Search products...">
            </div>
            <div class="relative inline-block text-left">
                <select class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <?php
                    $categories = ['All Categories', 'Stationery', 'Computers', 'Accessories'];
                    foreach ($categories as $category): ?>
                    <option value="<?= $category === 'All Categories' ? '' : $category ?>">
                        <?= $category ?>
                    </option>
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
            <tbody class="bg-white divide-y divide-gray-200">
                <?php
                $products = [
                    ['id' => 1, 'name' => 'HP Printer Ink (Black)', 'category' => 'Accessories', 'stock' => 5, 'minRequired' => 10, 'price' => '$45.99', 'lastUpdated' => '2023-07-10'],
                    ['id' => 2, 'name' => 'A4 Paper Reams', 'category' => 'Stationery', 'stock' => 12, 'minRequired' => 20, 'price' => '$4.99', 'lastUpdated' => '2023-07-12'],
                    ['id' => 3, 'name' => 'Dell Latitude Laptop', 'category' => 'Computers', 'stock' => 8, 'minRequired' => 5, 'price' => '$1,299.99', 'lastUpdated' => '2023-07-05'],
                    ['id' => 4, 'name' => 'Wireless Mouse', 'category' => 'Accessories', 'stock' => 23, 'minRequired' => 15, 'price' => '$24.99', 'lastUpdated' => '2023-07-08'],
                    ['id' => 5, 'name' => 'USB-C Cables', 'category' => 'Accessories', 'stock' => 42, 'minRequired' => 20, 'price' => '$12.99', 'lastUpdated' => '2023-07-15'],
                    ['id' => 6, 'name' => 'Stapler', 'category' => 'Stationery', 'stock' => 18, 'minRequired' => 10, 'price' => '$8.99', 'lastUpdated' => '2023-07-11'],
                    ['id' => 7, 'name' => 'Ballpoint Pens (Blue)', 'category' => 'Stationery', 'stock' => 145, 'minRequired' => 50, 'price' => '$0.99', 'lastUpdated' => '2023-07-09'],
                    ['id' => 8, 'name' => 'Wireless Keyboard', 'category' => 'Accessories', 'stock' => 15, 'minRequired' => 10, 'price' => '$49.99', 'lastUpdated' => '2023-07-13']
                ];
                
                foreach ($products as $product): 
                    $stockClass = $product['stock'] < $product['minRequired'] ? 'text-red-500 font-medium' : 'text-gray-500';
                ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $product['name'] ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $product['category'] ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <span class="mr-2 <?= $stockClass ?>"><?= $product['stock'] ?></span>
                            <div class="flex space-x-1">
                                <button onclick="adjustStock(<?= $product['id'] ?>, -1)" class="text-gray-500 hover:text-gray-700">
                                    <?= getIconSvg('arrow-down', 'h-4 w-4') ?>
                                </button>
                                <button onclick="adjustStock(<?= $product['id'] ?>, 1)" class="text-gray-500 hover:text-gray-700">
                                    <?= getIconSvg('arrow-up', 'h-4 w-4') ?>
                                </button>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $product['minRequired'] ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $product['price'] ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $product['lastUpdated'] ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <button onclick="openAdjustmentModal(<?= htmlspecialchars(json_encode($product)) ?>)" class="text-blue-600 hover:text-blue-900 bg-blue-50 px-2 py-1 rounded">
                            Adjust Quantity
                        </button>
                        <button onclick="openModal('edit', <?= htmlspecialchars(json_encode(['id' => $product['id'], 'name' => $product['name']])) ?>)" class="text-blue-600 hover:text-blue-900">
                            <?= getIconSvg('edit', 'h-4 w-4') ?>
                        </button>
                        <button onclick="confirmDelete(<?= $product['id'] ?>)" class="text-red-600 hover:text-red-900">
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
            Showing <span class="font-medium">1</span> to <span class="font-medium">8</span> of <span class="font-medium">24</span> results
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

<!-- Success Message -->
<div id="successMessage" class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50 hidden"></div>

<!-- Product Modal -->
<div id="productModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true" onclick="closeModal()">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <?= getIconSvg('box', 'h-6 w-6 text-blue-600') ?>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 id="modalTitle" class="text-lg leading-6 font-medium text-gray-900"></h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label for="productName" class="block text-sm font-medium text-gray-700">Product Name</label>
                                <input type="text" id="productName" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Enter product name">
                            </div>
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                                <select id="category" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="">Select a category</option>
                                    <option value="Stationery">Stationery</option>
                                    <option value="Computers">Computers</option>
                                    <option value="Accessories">Accessories</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="stock" class="block text-sm font-medium text-gray-700">Initial Stock</label>
                                    <input type="number" id="stock" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="0" min="0">
                                </div>
                                <div>
                                    <label for="minRequired" class="block text-sm font-medium text-gray-700">Min Required</label>
                                    <input type="number" id="minRequired" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="0" min="0">
                                </div>
                            </div>
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="text" id="price" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="saveProduct" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Add
                </button>
                <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Quantity Adjustment Modal -->
<div id="adjustmentModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true" onclick="closeAdjustmentModal()">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <?= getIconSvg('box', 'h-6 w-6 text-blue-600') ?>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Adjust Stock Quantity</h3>
                        <div class="mt-2">
                            <p id="currentStockText" class="text-sm text-gray-500"></p>
                        </div>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Adjustment Type</label>
                                <div class="mt-2 flex space-x-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" class="form-radio" name="adjustmentType" value="add" checked>
                                        <span class="ml-2">Add</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" class="form-radio" name="adjustmentType" value="deduct">
                                        <span class="ml-2">Deduct</span>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Quantity to adjust</label>
                                <input type="number" id="adjustmentQuantity" min="0" value="0" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div class="text-sm text-gray-500">
                                New stock will be: <span id="newStockValue"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="submitAdjustment()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Confirm Adjustment
                </button>
                <button type="button" onclick="closeAdjustmentModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
 </main>
    </div>
</body>
</html>

<script>
// Global variables
let currentAdjustingProduct = null;

// Product modal functions
function openModal(mode, product = null) {
    const modal = document.getElementById('productModal');
    const modalTitle = document.getElementById('modalTitle');
    const saveButton = document.getElementById('saveProduct');
    
    if (mode === 'add') {
        modalTitle.textContent = 'Add New Product';
        saveButton.textContent = 'Add';
        // Clear form
        document.getElementById('productName').value = '';
        document.getElementById('category').value = '';
        document.getElementById('stock').value = '';
        document.getElementById('minRequired').value = '';
        document.getElementById('price').value = '';
    } else if (mode === 'edit' && product) {
        modalTitle.textContent = 'Edit Product';
        saveButton.textContent = 'Save';
        // Fill form with product data
        document.getElementById('productName').value = product.name;
        // In a real app, you would fill other fields from the product data
    }
    
    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('productModal').classList.add('hidden');
}

// Stock adjustment functions
function openAdjustmentModal(product) {
    currentAdjustingProduct = product;
    const modal = document.getElementById('adjustmentModal');
    const currentStockText = document.getElementById('currentStockText');
    
    currentStockText.textContent = `Current stock for ${product.name}: ${product.stock}`;
    document.getElementById('adjustmentQuantity').value = 0;
    document.querySelector('input[name="adjustmentType"][value="add"]').checked = true;
    updateNewStockValue();
    
    modal.classList.remove('hidden');
}

function closeAdjustmentModal() {
    document.getElementById('adjustmentModal').classList.add('hidden');
    currentAdjustingProduct = null;
}

function updateNewStockValue() {
    if (!currentAdjustingProduct) return;
    
    const adjustmentType = document.querySelector('input[name="adjustmentType"]:checked').value;
    const quantity = parseInt(document.getElementById('adjustmentQuantity').value) || 0;
    const newStock = adjustmentType === 'add' 
        ? currentAdjustingProduct.stock + quantity 
        : currentAdjustingProduct.stock - quantity;
    
    document.getElementById('newStockValue').textContent = Math.max(0, newStock);
}

// Event listeners for adjustment modal
document.querySelectorAll('input[name="adjustmentType"]').forEach(radio => {
    radio.addEventListener('change', updateNewStockValue);
});

document.getElementById('adjustmentQuantity').addEventListener('input', updateNewStockValue);

function submitAdjustment() {
    if (!currentAdjustingProduct) return;
    
    const adjustmentType = document.querySelector('input[name="adjustmentType"]:checked').value;
    const quantity = parseInt(document.getElementById('adjustmentQuantity').value) || 0;
    
    if (quantity <= 0) {
        alert('Please enter a valid quantity');
        return;
    }
    
    const finalAdjustment = adjustmentType === 'add' ? quantity : -quantity;
    const newQuantity = currentAdjustingProduct.stock + finalAdjustment;
    
    if (newQuantity < 0) {
        alert('Stock quantity cannot be negative');
        return;
    }
    
    // In a real app, you would make an AJAX call to update the stock
    showSuccessMessage(`Successfully ${adjustmentType === 'add' ? 'added' : 'deducted'} ${quantity} units from ${currentAdjustingProduct.name}`);
    closeAdjustmentModal();
}

// Helper functions
function adjustStock(productId, adjustment) {
    // In a real app, you would make an AJAX call to update the stock
    showSuccessMessage(`Stock adjusted by ${adjustment > 0 ? '+' : ''}${adjustment}`);
}

function confirmDelete(productId) {
    if (confirm('Are you sure you want to delete this product?')) {
        // In a real app, you would make an AJAX call to delete the product
        showSuccessMessage('Product deleted successfully');
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

// Add these icons to your getIconSvg function if not already present
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