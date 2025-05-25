<?php
require 'config.php';
$title = 'Admin Dashboard';

/* if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
} */

session_start();

// Check if user is logged in



$username = $_SESSION['user']['username'] ?? 'User';

$user_id = $_SESSION['user_id'];
$_SESSION['last_activity'] = time();

// Fetch statistics from database
$stats = [];
$result = $conn->query("SELECT COUNT(*) as total FROM products");
$stats['total_products'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as low FROM products WHERE quantity < minimum_stock");
$stats['low_stock'] = $result->fetch_assoc()['low'];

$result = $conn->query("SELECT COUNT(*) as total FROM users");
$stats['total_users'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM categories");
$stats['total_categories'] = $result->fetch_assoc()['total'];

// Fetch low stock items
$lowStockItems = [];
$sql = "SELECT p.product_id, p.product_name, c.category_name, p.quantity, p.minimum_stock 
        FROM products p 
        JOIN categories c ON p.category_id = c.category_id 
        WHERE p.quantity < p.minimum_stock 
        ORDER BY (p.quantity/p.minimum_stock) ASC 
        LIMIT 5";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $lowStockItems[] = $row;
    }
}

// Fetch recent stock movements
$recentMovements = [];
$sql = "SELECT s.update_id, p.product_name, s.update_type, s.quantity, s.created_at, u.user_name 
        FROM stock_update s
        JOIN products p ON s.product_id = p.product_id
        JOIN users u ON s.user_id = u.user_id
        ORDER BY s.created_at DESC 
        LIMIT 5";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $recentMovements[] = $row;
    }
}

// Fetch stock by category
$stockByCategory = [];
$sql = "SELECT c.category_id, c.category_name, SUM(p.quantity) as total_items 
        FROM products p
        JOIN categories c ON p.category_id = c.category_id
        GROUP BY c.category_id, c.category_name";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $stockByCategory[] = $row;
    }
}

// Calculate max height for chart (for scaling)
$maxItems = max(array_column($stockByCategory, 'total_items')) ?: 1;
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
        .chart-bar {
            transition: height 0.5s ease;
        }
        .alert-badge {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
    </style>
</head>
<body class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'header.php'; ?>
        
        <main class="content-area">
            <!-- Success/Error Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

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
                $statCards = [
                    [
                        'title' => 'Total Products',
                        'value' => number_format($stats['total_products']),
                        'icon' => 'box',
                        'color' => 'blue',
                        'change' => '+5%', // This would come from a comparison query in a real app
                        'changeType' => 'increase'
                    ],
                    [
                        'title' => 'Low Stock Items',
                        'value' => number_format($stats['low_stock']),
                        'icon' => 'alert-triangle',
                        'color' => 'red',
                        'change' => $stats['low_stock'] > 0 ? '+'.number_format($stats['low_stock']) : '0',
                        'changeType' => $stats['low_stock'] > 0 ? 'increase' : 'neutral'
                    ],
                    [
                        'title' => 'Total Users',
                        'value' => number_format($stats['total_users']),
                        'icon' => 'users',
                        'color' => 'green',
                        'change' => '0', // This would come from a comparison query in a real app
                        'changeType' => 'neutral'
                    ],
                    [
                        'title' => 'Categories',
                        'value' => number_format($stats['total_categories']),
                        'icon' => 'clipboard-list',
                        'color' => 'purple',
                        'change' => '+1', // This would come from a comparison query in a real app
                        'changeType' => 'increase'
                    ]
                ];

                foreach ($statCards as $stat): 
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
                        <?php if (count($lowStockItems) > 0): ?>
                            <span class="alert-badge bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                <?= count($lowStockItems) ?> Alert<?= count($lowStockItems) > 1 ? 's' : '' ?>
                            </span>
                        <?php else: ?>
                            <?= getIconSvg('check-circle', 'h-5 w-5 text-green-500') ?>
                        <?php endif; ?>
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
                                <?php if (count($lowStockItems) > 0): ?>
                                    <?php foreach ($lowStockItems as $item): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($item['product_name']) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($item['category_name']) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-500 font-medium"><?= $item['quantity'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $item['minimum_stock'] ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No low stock items found</td>
                                    </tr>
                                <?php endif; ?>
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (count($recentMovements) > 0): ?>
                                    <?php foreach ($recentMovements as $movement): 
                                        $typeClass = $movement['update_type'] === 'increment' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                        $typeText = $movement['update_type'] === 'increment' ? 'Received' : 'Dispatched';
                                    ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($movement['product_name']) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $typeClass ?>">
                                                <?= $typeText ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $movement['quantity'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('Y-m-d', strtotime($movement['created_at'])) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($movement['user_name']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No recent stock movements found</td>
                                    </tr>
                                <?php endif; ?>
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
                    <?php if (count($stockByCategory) > 0): ?>
                        <?php 
                        $colors = ['blue', 'green', 'yellow', 'purple', 'indigo', 'pink', 'red'];
                        $colorIndex = 0;
                        ?>
                        <?php foreach ($stockByCategory as $category): 
                            $height = ($category['total_items'] / $maxItems) * 180; // Scale to max 180px
                            $color = $colors[$colorIndex % count($colors)];
                            $colorIndex++;
                        ?>
                        <div class="flex flex-col items-center">
                            <div class="bg-<?= $color ?>-500 w-16 rounded-t chart-bar" style="height: <?= $height ?>px"></div>
                            <p class="mt-2 text-sm font-medium"><?= htmlspecialchars($category['category_name']) ?></p>
                            <p class="text-xs text-gray-500"><?= number_format($category['total_items']) ?> items</p>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="w-full text-center text-gray-500">
                            No category data available
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

<script>
// Simple animation for chart bars when page loads
document.addEventListener('DOMContentLoaded', function() {
    const bars = document.querySelectorAll('.chart-bar');
    bars.forEach(bar => {
        const originalHeight = bar.style.height;
        bar.style.height = '0';
        setTimeout(() => {
            bar.style.height = originalHeight;
        }, 100);
    });
});

// Tab functionality (if you implement tabs later)
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
</script>

<?php
// Icon helper function
function getIconSvg($iconName, $classes = '') {
    $icons = [
        'box' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>',
        'alert-triangle' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
        'users' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
        'clipboard-list' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect><path d="M9 14h6"></path><path d="M9 10h6"></path></svg>',
        'package' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>',
        'bar-chart-2' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>',
        'check-circle' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>'
    ];
    
    return $icons[$iconName] ?? '';
}