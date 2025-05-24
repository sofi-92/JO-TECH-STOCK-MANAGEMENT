<?php
// reports.php
$title = "Reports";
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
        /* Minimal utility classes used in the markup */
        .bg-white { background: #fff; }
        .bg-gray-50 { background: #f9fafb; }
        .bg-green-100 { background: #d1fae5; }
        .bg-red-100 { background: #fee2e2; }
        .bg-blue-500 { background: #3b82f6; }
        .bg-green-500 { background: #10b981; }
        .bg-yellow-500 { background: #f59e42; }
        .bg-purple-500 { background: #a78bfa; }
        .text-gray-600 { color: #4b5563; }
        .text-gray-500 { color: #6b7280; }
        .text-gray-700 { color: #374151; }
        .text-gray-900 { color: #111827; }
        .text-green-800 { color: #065f46; }
        .text-red-800 { color: #991b1b; }
        .text-blue-500 { color: #3b82f6; }
        .text-blue-600 { color: #2563eb; }
        .text-sm { font-size: 0.875rem; }
        .text-xs { font-size: 0.75rem; }
        .text-lg { font-size: 1.125rem; }
        .font-semibold { font-weight: 600; }
        .font-medium { font-weight: 500; }
        .rounded-lg { border-radius: 0.5rem; }
        .rounded-md { border-radius: 0.375rem; }
        .rounded-t { border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem; }
        .rounded-full { border-radius: 9999px; }
        .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); }
        .mb-6 { margin-bottom: 1.5rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mt-2 { margin-top: 0.5rem; }
        .mr-1 { margin-right: 0.25rem; }
        .mr-2 { margin-right: 0.5rem; }
        .space-x-2 > :not(:last-child) { margin-right: 0.5rem; }
        .space-x-4 > :not(:last-child) { margin-right: 1rem; }
        .flex { display: flex; }
        .flex-col { flex-direction: column; }
        .flex-row { flex-direction: row; }
        .items-center { align-items: center; }
        .items-end { align-items: flex-end; }
        .justify-between { justify-content: space-between; }
        .justify-around { justify-content: space-around; }
        .justify-center { justify-content: center; }
        .inline-flex { display: inline-flex; }
        .overflow-x-auto { overflow-x: auto; }
        .overflow-y-auto { overflow-y: auto; }
        .hidden { display: none; }
        .block { display: block; }
        .w-full { width: 100%; }
        .w-24 { width: 6rem; }
        .h-4 { height: 1rem; }
        .h-5 { height: 1.25rem; }
        .h-64 { height: 16rem; }
        .min-w-full { min-width: 100%; }
        .p-4{padding: 1rem;}
        .p-6 { padding: 1.5rem; }
        .py-4 { padding-top: 1rem; padding-bottom: 1rem; }
        .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
        .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
        .py-1\.5 { padding-top: 0.375rem; padding-bottom: 0.375rem; }
        .py-0\.5 { padding-top: 0.125rem; padding-bottom: 0.125rem; }
        .px-1 { padding-left: 0.25rem; padding-right: 0.25rem; }
        .px-2\.5 { padding-left: 0.625rem; padding-right: 0.625rem; }
        .px-3 { padding-left: 0.75rem; padding-right: 0.75rem; }
        .px-4 { padding-left: 1rem; padding-right: 1rem; }
        .px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
        .pl-3 { padding-left: 0.75rem; }
        .pr-10 { padding-right: 2.5rem; }
        .border { border-width: 1px; border-style: solid; border-color: #d1d5db; }
        .border-b { border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: #e5e7eb; }
        .border-gray-200 { border-color: #e5e7eb; }
        .border-gray-300 { border-color: #d1d5db; }
        .border-blue-500 { border-color: #3b82f6; }
        .border-transparent { border-color: transparent; }
        .rounded-md { border-radius: 0.375rem; }
        .focus\:outline-none:focus { outline: none; }
        .focus\:ring-2:focus { box-shadow: 0 0 0 2px #2563eb33; }
        .focus\:ring-offset-2:focus { box-shadow: 0 0 0 4px #fff, 0 0 0 6px #2563eb33; }
        .focus\:ring-blue-500:focus { box-shadow: 0 0 0 2px #3b82f6; }
        .hover\:bg-blue-700:hover { background: #1d4ed8; }
        .hover\:bg-gray-50:hover { background: #f9fafb; }
        .hover\:text-gray-700:hover { color: #374151; }
        .hover\:border-gray-300:hover { border-color: #d1d5db; }
        .sm\:text-sm { font-size: 0.875rem; }
        .whitespace-nowrap { white-space: nowrap; }
        .divide-y > :not(:first-child) { border-top: 1px solid #e5e7eb; }
        .divide-gray-200 > :not(:first-child) { border-top: 1px solid #e5e7eb; }
        .uppercase { text-transform: uppercase; }
        .tracking-wider { letter-spacing: 0.05em; }
        /* Grid utility for 1 column */
        .grid { display: grid; }
        .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
        .gap-6 { gap: 1.5rem; }
        .gap-8 { gap: 2rem; }
        .gap-4 { gap: 1rem; }
        .gap-2 { gap: 0.5rem; }
        
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
        <h2 class="text-lg font-semibold mb-4">Reports</h2>
        <p class="text-gray-600">
            Generate and export reports on stock levels and inventory status.
        </p>
    </div>
</div>

<div class="bg-white p-6 rounded-lg shadow-sm mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div class="flex items-center mb-4 md:mb-0">
            <?= getIconSvg('file-text', 'h-5 w-5 text-blue-500 mr-2') ?>
            <h2 class="text-lg font-semibold">Generate Reports</h2>
        </div>
        <div class="flex space-x-4">
            <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <?= getIconSvg('download', 'h-4 w-4 mr-2') ?>
                Export as PDF
            </button>
            <button class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <?= getIconSvg('download', 'h-4 w-4 mr-2') ?>
                Export as Excel
            </button>
        </div>
    </div>
    
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <button onclick="showTab('stock-levels')" id="stock-levels-tab" class="border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Stock Levels
                </button>
                <button onclick="showTab('low-stock')" id="low-stock-tab" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Low Stock
                </button>
                <button onclick="showTab('category')" id="category-tab" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Category Reports
                </button>
            </nav>
        </div>
    </div>
    
    <!-- Stock Levels Tab -->
    <div id="stock-levels-content" class="tab-content">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Current Stock Levels</h3>
            <div class="flex items-center">
                <span class="text-sm text-gray-500 mr-2">Filter:</span>
                <select class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option value="">All Categories</option>
                    <option value="Stationery">Stationery</option>
                    <option value="Computers">Computers</option>
                    <option value="Accessories">Accessories</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min Required</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    $stockLevelsData = [
                        ['id' => 1, 'name' => 'HP Printer Ink (Black)', 'category' => 'Accessories', 'stock' => 5, 'minRequired' => 10, 'status' => 'Low Stock'],
                        ['id' => 2, 'name' => 'A4 Paper Reams', 'category' => 'Stationery', 'stock' => 12, 'minRequired' => 20, 'status' => 'Low Stock'],
                        ['id' => 3, 'name' => 'Dell Latitude Laptop', 'category' => 'Computers', 'stock' => 8, 'minRequired' => 5, 'status' => 'In Stock'],
                        ['id' => 4, 'name' => 'Wireless Mouse', 'category' => 'Accessories', 'stock' => 23, 'minRequired' => 15, 'status' => 'In Stock'],
                        ['id' => 5, 'name' => 'USB-C Cables', 'category' => 'Accessories', 'stock' => 42, 'minRequired' => 20, 'status' => 'In Stock'],
                        ['id' => 6, 'name' => 'Stapler', 'category' => 'Stationery', 'stock' => 18, 'minRequired' => 10, 'status' => 'In Stock'],
                        ['id' => 7, 'name' => 'Ballpoint Pens (Blue)', 'category' => 'Stationery', 'stock' => 145, 'minRequired' => 50, 'status' => 'In Stock'],
                        ['id' => 8, 'name' => 'Wireless Keyboard', 'category' => 'Accessories', 'stock' => 15, 'minRequired' => 10, 'status' => 'In Stock']
                    ];
                    
                    foreach ($stockLevelsData as $item): 
                        $statusClass = $item['status'] === 'In Stock' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                    ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $item['name'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $item['category'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $item['stock'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $item['minRequired'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusClass ?>">
                                <?= $item['status'] ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Low Stock Tab -->
    <div id="low-stock-content" class="tab-content hidden">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Low Stock Items</h3>
            <div class="flex items-center">
                <span class="text-sm text-gray-500 mr-2">Filter:</span>
                <select class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option value="">All Categories</option>
                    <option value="Stationery">Stationery</option>
                    <option value="Computers">Computers</option>
                    <option value="Accessories">Accessories</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min Required</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shortage</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    $lowStockData = array_filter($stockLevelsData, function($item) {
                        return $item['stock'] < $item['minRequired'];
                    });
                    
                    foreach ($lowStockData as $item): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $item['name'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $item['category'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-500 font-medium"><?= $item['stock'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $item['minRequired'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-500"><?= $item['minRequired'] - $item['stock'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Category Reports Tab -->
    <div id="category-content" class="tab-content hidden">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Category Reports</h3>
            <div class="flex items-center">
                <span class="text-sm text-gray-500 mr-2">View as:</span>
                <div class="flex space-x-2">
                    <button onclick="showChart('bar')" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <?= getIconSvg('bar-chart-2', 'h-4 w-4 mr-1') ?>
                        Bar
                    </button>
                    <button onclick="showChart('pie')" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <?= getIconSvg('pie-chart', 'h-4 w-4 mr-1') ?>
                        Pie
                    </button>
                </div>
            </div>
        </div>
        
        <div class="mb-6">
            <div class="bg-white p-6 rounded-lg">
                <h3 class="text-lg font-medium mb-4">Stock by Category</h3>
                <div id="bar-chart" class="h-64 flex items-end justify-around">
                    <div class="flex flex-col items-center">
                        <div class="bg-blue-500 w-24 rounded-t" style="height: 150px"></div>
                        <p class="mt-2 text-sm font-medium">Stationery</p>
                        <p class="text-xs text-gray-500">543 items</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="bg-green-500 w-24 rounded-t" style="height: 100px"></div>
                        <p class="mt-2 text-sm font-medium">Computers</p>
                        <p class="text-xs text-gray-500">328 items</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="bg-yellow-500 w-24 rounded-t" style="height: 120px"></div>
                        <p class="mt-2 text-sm font-medium">Accessories</p>
                        <p class="text-xs text-gray-500">413 items</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="bg-purple-500 w-24 rounded-t" style="height: 60px"></div>
                        <p class="mt-2 text-sm font-medium">Others</p>
                        <p class="text-xs text-gray-500">124 items</p>
                    </div>
                </div>
                <div id="pie-chart" class="hidden h-64 flex items-center justify-center">
                    <!-- Pie chart would be implemented with a chart library in a real app -->
                    <p class="text-gray-500">Pie chart visualization would appear here</p>
                </div>
            </div>
        </div>
        
        <div>
            <h3 class="text-lg font-medium mb-4">Category Summary</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Items</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Low Stock Items</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Value</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Stationery</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">543</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$4,325.75</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Computers</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">328</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">0</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$152,845.50</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Accessories</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">413</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">3</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$12,648.25</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Others</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">124</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">0</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$2,156.80</td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Total</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">1,408</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">5</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">$171,976.30</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
 </main>
    </div>
</body>
</html>

<script>
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Show selected tab content
    document.getElementById(tabName + '-content').classList.remove('hidden');
    
    // Update tab styling
    document.querySelectorAll('nav button').forEach(tab => {
        tab.classList.remove('border-blue-500', 'text-blue-600');
        tab.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById(tabName + '-tab').classList.remove('border-transparent', 'text-gray-500');
    document.getElementById(tabName + '-tab').classList.add('border-blue-500', 'text-blue-600');
}

function showChart(chartType) {
    if (chartType === 'bar') {
        document.getElementById('bar-chart').classList.remove('hidden');
        document.getElementById('pie-chart').classList.add('hidden');
    } else {
        document.getElementById('bar-chart').classList.add('hidden');
        document.getElementById('pie-chart').classList.remove('hidden');
    }
}

// Initialize with first tab shown
document.addEventListener('DOMContentLoaded', function() {
    showTab('stock-levels');
});
</script>

<?php


// Add these icons to your getIconSvg function if not already present
function getIconSvg($iconName, $classes = '') {
    $icons = [
        'file-text' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line></svg>',
        'download' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>',
        'bar-chart-2' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>',
        'pie-chart' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path><path d="M22 12A10 10 0 0 0 12 2v10z"></path></svg>'
    ];
    
    return $icons[$iconName] ?? '';
}
?>