<?php
// categories.php
$title = "Product Categories";
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
        /* Utility classes */
        .bg-white { background: #fff; }
        .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); }
        .rounded-lg { border-radius: 0.5rem; }
        .p-4 { padding: 1rem; }
        .p-6 { padding: 1.5rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .text-lg { font-size: 1.125rem; }
        .font-semibold { font-weight: 600; }
        .text-white{ color: #fff; }
        .text-gray-600 { color: #4b5563; }
        .text-gray-500 { color: #6b7280; }
        .text-gray-700 { color: #374151; }
        .text-gray-900 { color: #111827; }
        .text-blue-500 { color: #3b82f6; }
        .text-blue-600 { color: #2563eb; }
        .text-blue-700 { color: #1d4ed8; }
        .text-red-600 { color: #dc2626; }
        .text-red-900 { color: #7f1d1d; }
        .bg-blue-600 { background: #2563eb; }
        .bg-blue-700 { background: #1d4ed8; }
        .hover\:bg-blue-700:hover { background: #1d4ed8; }
        .hover\:bg-gray-50:hover { background: #f9fafb; }
        .hover\:text-blue-900:hover { color: #1e40af; }
        .hover\:text-red-900:hover { color: #7f1d1d; }
        .bg-gray-50 { background: #f9fafb; }
        .bg-gray-500 { background: #6b7280; }
        .bg-blue-100 { background: #dbeafe; }
        .border { border-width: 1px; border-style: solid; border-color: #d1d5db; }
        .border-gray-300 { border-color: #d1d5db; }
        .border-transparent { border-color: transparent; }
        .rounded-md { border-radius: 0.375rem; }
        .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); }
        .w-full { width: 100%; }
        .h-4 { height: 1rem; }
        .w-4 { width: 1rem; }
        .h-5 { height: 1.25rem; }
        .w-5 { width: 1.25rem; }
        .h-6 { height: 1.5rem; }
        .w-6 { width: 1.5rem; }
        .mr-2 { margin-right: 0.5rem; }
        .mr-4 { margin-right: 1rem; }
        .ml-3 { margin-left: 0.75rem; }
        .ml-4 { margin-left: 1rem; }
        .mt-1 { margin-top: 0.25rem; }
        .mt-3 { margin-top: 0.75rem; }
        .mt-4 { margin-top: 1rem; }
        .mb-0 { margin-bottom: 0; }
        .space-y-4 > :not([hidden]) ~ :not([hidden]) { margin-top: 1rem; }
        .space-x-4 > :not([hidden]) ~ :not([hidden]) { margin-left: 1rem; }
        .flex { display: flex; }
        .flex-col { flex-direction: column; }
        .flex-row { flex-direction: row; }
        .items-center { align-items: center; }
        .items-start { align-items: flex-start; }
        .justify-between { justify-content: space-between; }
        .justify-center { justify-content: center; }
        .inline-flex { display: inline-flex; }
        .inline-block { display: inline-block; }
        .block { display: block; }
        .hidden { display: none; }
        .overflow-x-auto { overflow-x: auto; }
        .overflow-y-auto { overflow-y: auto; }
        .overflow-hidden { overflow: hidden; }
        .min-w-full { min-width: 100%; }
        .min-h-screen { min-height: 100vh; }
        .max-w-lg { max-width: 32rem; }
        .sm\:block { display: block; }
        .sm\:inline-block { display: inline-block; }
        .sm\:align-middle { vertical-align: middle; }
        .sm\:h-10 { height: 2.5rem; }
        .sm\:w-10 { width: 2.5rem; }
        .sm\:my-8 { margin-top: 2rem; margin-bottom: 2rem; }
        .sm\:ml-3 { margin-left: 0.75rem; }
        .sm\:mt-0 { margin-top: 0; }
        .sm\:w-auto { width: auto; }
        .sm\:text-sm { font-size: 0.875rem; }
        .sm\:p-0 { padding: 0; }
        .sm\:pb-4 { padding-bottom: 1rem; }
        .sm\:px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
        .sm\:flex { display: flex; }
        .sm\:items-start { align-items: flex-start; }
        .sm\:justify-center { justify-content: center; }
        .sm\:ml-4 { margin-left: 1rem; }
        .sm\:mt-0 { margin-top: 0; }
        .sm\:ml-3 { margin-left: 0.75rem; }
        .sm\:w-auto { width: auto; }
        .sm\:text-sm { font-size: 0.875rem; }
        .sm\:p-6 { padding: 1.5rem; }
        .sm\:pb-4 { padding-bottom: 1rem; }
        .sm\:my-8 { margin-top: 2rem; margin-bottom: 2rem; }
        .sm\:align-middle { vertical-align: middle; }
        .sm\:max-w-lg { max-width: 32rem; }
        .sm\:w-full { width: 100%; }
        .pointer-events-none { pointer-events: none; }
        .absolute { position: absolute; }
        .relative { position: relative; }
        .fixed { position: fixed; }
        .inset-0 { top: 0; right: 0; bottom: 0; left: 0; }
        .inset-y-0 { top: 0; bottom: 0; }
        .left-0 { left: 0; }
        .right-0 { right: 0; }
        .top-0 { top: 0; }
        .bottom-0 { bottom: 0; }
        .z-10 { z-index: 10; }
        .z-50 { z-index: 50; }
        .rounded-full { border-radius: 9999px; }
        .whitespace-nowrap { white-space: nowrap; }
        .leading-5 { line-height: 1.25rem; }
        .leading-6 { line-height: 1.5rem; }
        .font-medium { font-weight: 500; }
        .font-semibold { font-weight: 600; }
        .uppercase { text-transform: uppercase; }
        .tracking-wider { letter-spacing: 0.05em; }
        .text-base { font-size: 1rem; }
        .text-xs { font-size: 0.75rem; }
        .shadow-xl { box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05); }
        .transition-opacity { transition: opacity 0.2s; }
        .transform { transform: none; }
        .focus\:outline-none:focus { outline: none; }
        .focus\:ring-1:focus { box-shadow: 0 0 0 1px #2563eb; }
        .focus\:ring-2:focus { box-shadow: 0 0 0 2px #2563eb; }
        .focus\:ring-blue-500:focus { box-shadow: 0 0 0 2px #3b82f6; }
        .focus\:border-blue-500:focus { border-color: #3b82f6; }
        .focus\:ring-offset-2:focus { box-shadow: 0 0 0 2px #fff, 0 0 0 4px #2563eb; }
        .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
        .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
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
            <?= getIconSvg('tag', 'h-5 w-5 text-blue-500 mr-2') ?>
            <h2 class="text-lg font-semibold">Categories</h2>
        </div>
        <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <?= getIconSvg('search', 'h-5 w-5 text-gray-400') ?>
                </div>
                <input type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Search categories..." />
            </div>
            <button onclick="openModal('add')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <?= getIconSvg('plus', 'h-4 w-4 mr-2') ?>
                Add Category
            </button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Products</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php
                $categories = [
                    ['id' => 1, 'name' => 'Stationery', 'description' => 'Office supplies like pens, papers, notebooks', 'productCount' => 543, 'createdAt' => '2023-01-15'],
                    ['id' => 2, 'name' => 'Computers', 'description' => 'Laptops, desktops, and computer parts', 'productCount' => 328, 'createdAt' => '2023-01-15'],
                    ['id' => 3, 'name' => 'Accessories', 'description' => 'Computer peripherals and accessories', 'productCount' => 413, 'createdAt' => '2023-02-20'],
                    ['id' => 4, 'name' => 'Printers', 'description' => 'Printers and printing supplies', 'productCount' => 124, 'createdAt' => '2023-03-05'],
                    ['id' => 5, 'name' => 'Networking', 'description' => 'Networking equipment and cables', 'productCount' => 89, 'createdAt' => '2023-04-10']
                ];
                
                foreach ($categories as $category): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $category['name'] ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $category['description'] ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $category['productCount'] ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $category['createdAt'] ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button onclick="openModal('edit', <?= htmlspecialchars(json_encode($category)) ?>)" class="text-blue-600 hover:text-blue-900 mr-4">
                            <?= getIconSvg('edit', 'h-4 w-4') ?>
                        </button>
                        <button onclick="confirmDelete(<?= $category['id'] ?>)" class="text-red-600 hover:text-red-900">
                            <?= getIconSvg('trash-2', 'h-4 w-4') ?>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Category Modal -->
<div id="categoryModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true" onclick="closeModal()">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <?= getIconSvg('tag', 'h-6 w-6 text-blue-600') ?>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 id="modalTitle" class="text-lg leading-6 font-medium text-gray-900"></h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label for="categoryName" class="block text-sm font-medium text-gray-700">Category Name</label>
                                <input type="text" name="categoryName" id="categoryName" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Enter category name">
                            </div>
                            <div>
                                <label for="categoryDescription" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="categoryDescription" id="categoryDescription" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Enter category description"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="saveButton" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Add
                </button>
                <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
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
function openModal(mode, category = null) {
    const modal = document.getElementById('categoryModal');
    const modalTitle = document.getElementById('modalTitle');
    const saveButton = document.getElementById('saveButton');
    const categoryName = document.getElementById('categoryName');
    const categoryDescription = document.getElementById('categoryDescription');
    
    if (mode === 'add') {
        modalTitle.textContent = 'Add New Category';
        saveButton.textContent = 'Add';
        categoryName.value = '';
        categoryDescription.value = '';
    } else if (mode === 'edit' && category) {
        modalTitle.textContent = 'Edit Category';
        saveButton.textContent = 'Save';
        categoryName.value = category.name;
        categoryDescription.value = category.description;
    }
    
    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('categoryModal').classList.add('hidden');
}

function confirmDelete(categoryId) {
    if (confirm('Are you sure you want to delete this category?')) {
        // In a real app, you would submit a form or make an AJAX request to delete the category
        alert('Category would be deleted here (implementation needed)');
        // window.location.href = `delete_category.php?id=${categoryId}`;
    }
}

// Handle form submission
document.getElementById('saveButton').addEventListener('click', function() {
    const categoryName = document.getElementById('categoryName').value;
    const categoryDescription = document.getElementById('categoryDescription').value;
    
    if (!categoryName) {
        alert('Category name is required');
        return;
    }
    
    // In a real app, you would submit a form or make an AJAX request here
    alert(`Category would be saved here:\nName: ${categoryName}\nDescription: ${categoryDescription}`);
    closeModal();
});
</script>

<?php


// Add these icons to your getIconSvg function if not already present
function getIconSvg($iconName, $classes = '') {
    $icons = [
        'tag' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>',
        'search' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
        'plus' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>',
        'edit' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>',
        'trash-2' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>'
    ];
    
    return $icons[$iconName] ?? '';
}
?>