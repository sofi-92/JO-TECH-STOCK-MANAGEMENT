<?php
require 'config.php';
$title = 'Admin Dashboard';

// Session validation
/* if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'manager'])) {
    header('Location: login.php');
    exit;
}

// Session expiration (30 minutes)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
} */

$_SESSION['last_activity'] = time();
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
        .shadow-sm { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
        .rounded-lg { border-radius: 0.5rem; }
        .p-4 { padding: 1rem; }
        .p-6 { padding: 1.5rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .text-lg { font-size: 1.125rem; }
        .font-semibold { font-weight: 600; }
        .font-medium { font-weight: 500; }
        .text-gray-600 { color: #4b5563; }
        .text-gray-500 { color: #6b7280; }
        .text-gray-900 { color: #111827; }
        .text-2xl { font-size: 1.5rem; }
        .text-sm { font-size: 0.875rem; }
        .text-xs { font-size: 0.75rem; }
        .text-green-500 { color: #22c55e; }
        .text-green-800 { color: #166534; }
        .text-blue-500 { color: #3b82f6; }
        .text-blue-600 { color: #2563eb; }
        .text-red-500 { color: #ef4444; }
        .text-red-800 { color: #991b1b; }
        .text-purple-500 { color: #a21caf; }
        .bg-blue-500 { background: #3b82f6; }
        .bg-green-500 { background: #22c55e; }
        .bg-yellow-500 { background: #eab308; }
        .bg-purple-500 { background: #a21caf; }
        .bg-green-100 { background: #dcfce7; }
        .bg-red-100 { background: #fee2e2; }
        .rounded-t { border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem; }
        .rounded-full { border-radius: 9999px; }
        .flex { display: flex; }
        .flex-col { flex-direction: column; }
        .flex-row { flex-direction: row; }
        .items-center { align-items: center; }
        .items-end { align-items: flex-end; }
        .justify-between { justify-content: space-between; }
        .justify-around { justify-content: space-around; }
        .gap-6 { gap: 1.5rem; }
        .grid { display: grid; }
        .grid-cols-1 { grid-template-columns: 1fr; }
        .md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .lg\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .lg\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .h-60 { height: 15rem; }
        .h-6 { height: 1.5rem; }
        .h-5 { height: 1.25rem; }
        .w-5 { width: 1.25rem; }
        .w-6 { width: 1.5rem; }
        .w-16 { width: 4rem; }
        .ml-2 { margin-left: 0.5rem; }
        .mt-2 { margin-top: 0.5rem; }
        .overflow-x-auto { overflow-x: auto; }
        .overflow-y-auto { overflow-y: auto; }
        .min-w-full { min-width: 100%; }
        .divide-y > :not([hidden]) ~ :not([hidden]) { border-top: 1px solid #e5e7eb; }
        .divide-gray-200 > :not([hidden]) ~ :not([hidden]) { border-color: #e5e7eb; }
        .bg-gray-50 { background: #f9fafb; }
        .whitespace-nowrap { white-space: nowrap; }
        .px-2\.5 { padding-left: 0.625rem; padding-right: 0.625rem; }
        .px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
        .py-0\.5 { padding-top: 0.125rem; padding-bottom: 0.125rem; }
        .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
        .py-4 { padding-top: 1rem; padding-bottom: 1rem; }
        .tracking-wider { letter-spacing: 0.05em; }
        @media (min-width: 768px) {
            .md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (min-width: 1024px) {
            .lg\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .lg\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        }
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
            Welcome to JO TECH Stock Management System
        </h2>
        <p class="text-gray-600">
            As an administrator, you have full access to manage users, track
            inventory, categorize products, and generate reports.
        </p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Stats Cards -->
    <?php
    $stats = [
        [
            'title' => 'Total Products',
            'value' => '1,284',
            'icon' => 'box',
            'color' => 'blue',
            'change' => '+5%',
            'changeType' => 'increase'
        ],
        [
            'title' => 'Low Stock Items',
            'value' => '23',
            'icon' => 'alert-triangle',
            'color' => 'red',
            'change' => '+2',
            'changeType' => 'increase'
        ],
        [
            'title' => 'Total Users',
            'value' => '42',
            'icon' => 'users',
            'color' => 'green',
            'change' => '0',
            'changeType' => 'neutral'
        ],
        [
            'title' => 'Categories',
            'value' => '15',
            'icon' => 'clipboard-list',
            'color' => 'purple',
            'change' => '+1',
            'changeType' => 'increase'
        ]
    ];

    foreach ($stats as $stat): 
        $colorClass = "text-{$stat['color']}-600";
        $changeClass = $stat['changeType'] === 'increase' ? 'text-green-500' : 
                      ($stat['changeType'] === 'decrease' ? 'text-red-500' : 'text-gray-500');
    ?>
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-gray-500"><?= $stat['title'] ?></h3>
            <?= getIconSvg($stat['icon'], "h-6 w-6 $colorClass") ?>
        </div>
        <div class="flex items-end">
            <p class="text-2xl font-semibold"><?= $stat['value'] ?></p>
            <span class="ml-2 text-sm flex items-center <?= $changeClass ?>">
                <?= $stat['change'] ?>
            </span>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Low Stock Alerts -->
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">Low Stock Alerts</h2>
            <?= getIconSvg('alert-triangle', 'h-5 w-5 text-red-500') ?>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min Required</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    $lowStockItems = [
                        ['id' => 1, 'name' => 'HP Printer Ink (Black)', 'category' => 'Accessories', 'currentStock' => 5, 'minRequired' => 10],
                        ['id' => 2, 'name' => 'A4 Paper Reams', 'category' => 'Stationery', 'currentStock' => 12, 'minRequired' => 20],
                        ['id' => 3, 'name' => 'Dell Laptop Chargers', 'category' => 'Computers', 'currentStock' => 3, 'minRequired' => 5],
                        ['id' => 4, 'name' => 'Wireless Mice', 'category' => 'Accessories', 'currentStock' => 7, 'minRequired' => 15],
                        ['id' => 5, 'name' => 'Stapler Pins', 'category' => 'Stationery', 'currentStock' => 8, 'minRequired' => 15]
                    ];
                    
                    foreach ($lowStockItems as $item): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $item['name'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $item['category'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-500 font-medium"><?= $item['currentStock'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $item['minRequired'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Stock Movements -->
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">Recent Stock Movements</h2>
            <?= getIconSvg('package', 'h-5 w-5 text-blue-500') ?>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    $recentMovements = [
                        ['id' => 1, 'product' => 'Dell Latitude Laptop', 'type' => 'out', 'quantity' => 2, 'date' => '2023-07-15', 'user' => 'John Doe'],
                        ['id' => 2, 'product' => 'HP Printer Paper', 'type' => 'in', 'quantity' => 50, 'date' => '2023-07-14', 'user' => 'Jane Smith'],
                        ['id' => 3, 'product' => 'Ballpoint Pens (Blue)', 'type' => 'out', 'quantity' => 25, 'date' => '2023-07-13', 'user' => 'Mike Johnson'],
                        ['id' => 4, 'product' => 'USB-C Cables', 'type' => 'in', 'quantity' => 30, 'date' => '2023-07-12', 'user' => 'Sarah Williams'],
                        ['id' => 5, 'product' => 'Wireless Keyboards', 'type' => 'out', 'quantity' => 5, 'date' => '2023-07-11', 'user' => 'David Brown']
                    ];
                    
                    foreach ($recentMovements as $movement): 
                        $typeClass = $movement['type'] === 'in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                        $typeText = $movement['type'] === 'in' ? 'Received' : 'Dispatched';
                    ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $movement['product'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $typeClass ?>">
                                <?= $typeText ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $movement['quantity'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $movement['date'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Stock by Category -->
<div class="bg-white p-6 rounded-lg shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold">Stock by Category</h2>
        <?= getIconSvg('bar-chart-2', 'h-5 w-5 text-green-500') ?>
    </div>
    <div class="h-60 flex items-end justify-around">
        <div class="flex flex-col items-center">
            <div class="bg-blue-500 w-16 rounded-t" style="height: 120px"></div>
            <p class="mt-2 text-sm font-medium">Stationery</p>
            <p class="text-xs text-gray-500">543 items</p>
        </div>
        <div class="flex flex-col items-center">
            <div class="bg-green-500 w-16 rounded-t" style="height: 180px"></div>
            <p class="mt-2 text-sm font-medium">Computers</p>
            <p class="text-xs text-gray-500">328 items</p>
        </div>
        <div class="flex flex-col items-center">
            <div class="bg-yellow-500 w-16 rounded-t" style="height: 150px"></div>
            <p class="mt-2 text-sm font-medium">Accessories</p>
            <p class="text-xs text-gray-500">413 items</p>
        </div>
        <div class="flex flex-col items-center">
            <div class="bg-purple-500 w-16 rounded-t" style="height: 90px"></div>
            <p class="mt-2 text-sm font-medium">Others</p>
            <p class="text-xs text-gray-500">124 items</p>
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
/* $content = ob_get_clean();
include 'layout.php'; */

// Icon helper function (should be in a separate included file or at the top)
function getIconSvg($iconName, $classes = '') {
    $icons = [
        'box' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>',
        'alert-triangle' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
        'users' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
        'clipboard-list' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect><path d="M9 14h6"></path><path d="M9 10h6"></path></svg>',
        'package' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>',
        'bar-chart-2' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>'
    ];
    
    return $icons[$iconName] ?? '';
}
?>