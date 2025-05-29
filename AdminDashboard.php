<?php
require 'config.php';
$title = 'Admin Dashboard';

/* if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
} */

session_start();

// Check if user is logged in





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

$maxItems = !empty($stockByCategory) ? max(array_column($stockByCategory, 'total_items')) : 1;
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
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .card-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Stats Cards */
        .stat-card {
            position: relative;
            overflow: hidden;
        }

        .stat-content {
            position: relative;
            z-index: 2;
        }

        .stat-icon {
            position: absolute;
            right: 1rem;
            top: 1rem;
            opacity: 0.1;
            z-index: 1;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.8125rem;
            color: var(--gray);
            margin-bottom: 0.5rem;
            display: block;
        }

        .stat-change {
            display: inline-flex;
            align-items: center;
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }

        .stat-change.positive {
            background-color: #ecfdf5;
            color: #059669;
        }

        .stat-change.negative {
            background-color: #fef2f2;
            color: #dc2626;
        }

        .stat-change.neutral {
            background-color: #f3f4f6;
            color: #4b5563;
        }

        /* Tables */
        .table-container {
            border-radius: 8px;
            overflow-x: auto;
            
            border: 1px solid #e2e8f0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.75rem 1.25rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        td {
            padding: 0.875rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.875rem;
        }

        tr:last-child td {
            border-bottom: none;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.625rem;
            border-radius: 12px;
            font-size: 0.6875rem;
            font-weight: 600;
            letter-spacing: 0.25px;
        }

        .badge-success {
            background-color: #ecfdf5;
            color: #059669;
        }

        .badge-danger {
            background-color: #fef2f2;
            color: #dc2626;
        }

        /* Alerts */
        .alert {
            padding: 0.875rem 1rem;
            border-radius: 6px;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            font-size: 0.875rem;
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

        /* Charts */
        .chart-container {
            height: 200px;
            display: flex;
            align-items: flex-end;
            justify-content: space-around;
            padding: 1.5rem 0 2.5rem;
        }

        .chart-bar {
            transition: all 0.3s ease;
            width: 32px;
            border-radius: 4px 4px 0 0;
            position: relative;
            background: linear-gradient(to top, var(--primary), var(--primary-light));
        }

        .chart-bar:hover {
            opacity: 0.9;
        }

        .chart-label {
            position: absolute;
            bottom: -1.5rem;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.6875rem;
            font-weight: 500;
            white-space: nowrap;
            color: var(--gray);
        }

        .chart-value {
            position: absolute;
            top: -1.25rem;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.6875rem;
            font-weight: 600;
            color: var(--dark);
        }

        /* Icons */
        .icon {
            width: 18px;
            height: 18px;
            stroke-width: 1.75;
        }

        .icon-sm {
            width: 14px;
            height: 14px;
        }

        /* Grid */
        .grid {
            display: grid;
            gap: 1rem;
        }

        .grid-cols-1 {
            grid-template-columns: 1fr;
        }

        .grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .grid-cols-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        @media (max-width: 1024px) {
            .grid-cols-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .grid-cols-2, .grid-cols-4 {
                grid-template-columns: 1fr;
            }
            
            .content-area {
                padding: 1rem;
            }
        }

        /* Utility classes */
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 0.75rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-5 { margin-bottom: 1.25rem; }
        .mb-6 { margin-bottom: 1.5rem; }

        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .gap-2 { gap: 0.5rem; }
        .gap-3 { gap: 0.75rem; }

        .text-sm { font-size: 0.875rem; }
        .text-base { font-size: 1rem; }
        .text-lg { font-size: 1.125rem; }

        .font-medium { font-weight: 500; }
        .font-semibold { font-weight: 600; }
        .font-bold { font-weight: 700; }

        .text-gray-500 { color: var(--gray); }
        .text-gray-600 { color: #475569; }
        .text-blue-500 { color: var(--primary); }
        .text-green-500 { color: var(--success); }
        .text-red-500 { color: var(--danger); }


        .whitespace-nowrap { white-space: nowrap; }
    @media (min-width: 768px) {
        .md\:grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (min-width: 1024px) {
        .lg\:grid-cols-4 {
        grid-template-columns: repeat(4, minmax(0, 1fr));
        }
        .lg\:grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    </style>
</head>
<body class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'header.php'; ?>
        
        <main class="content-area">
            <!-- Alerts -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span><?= $_SESSION['success']; unset($_SESSION['success']); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span><?= $_SESSION['error']; unset($_SESSION['error']); ?></span>
                </div>
            <?php endif; ?>

            <!-- Welcome Card -->
            <div class="card mb-6">
                <div class="card-body">
                    <h2 class="text-lg font-semibold mb-2">Welcome to JO TECH Stock Management System</h2>
                    <p class="text-sm text-gray-600">
                        As an administrator, you have full access to manage users, track
                        inventory, categorize products, and generate reports.
                    </p>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <?php
                $statCards = [
                    [
                        'title' => 'Total Products',
                        'value' => number_format($stats['total_products']),
                        'icon' => 'box',
                        'color' => 'blue',
                        'change' => '+5%',
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
                        'change' => '0',
                        'changeType' => 'neutral'
                    ],
                    [
                        'title' => 'Categories',
                        'value' => number_format($stats['total_categories']),
                        'icon' => 'clipboard-list',
                        'color' => 'purple',
                        'change' => '+1',
                        'changeType' => 'increase'
                    ]
                ];

                foreach ($statCards as $stat): 
                    $changeClass = $stat['changeType'] === 'increase' ? 'positive' : 
                                  ($stat['changeType'] === 'decrease' ? 'negative' : 'neutral');
                ?>
                <div class="card stat-card">
                    <div class="stat-icon text-<?= $stat['color'] ?>-500">
                        <?= getIconSvg($stat['icon'], "w-8 h-8") ?>
                    </div>
                    <div class="card-body stat-content">
                        <div class="flex items-center justify-between mb-3">
                            <span class="stat-label"><?= $stat['title'] ?></span>
                            <?= getIconSvg($stat['icon'], "icon text-{$stat['color']}-500") ?>
                        </div>
                        <div class="flex items-end gap-2">
                            <span class="stat-number"><?= $stat['value'] ?></span>
                            <span class="stat-change <?= $changeClass ?>">
                                <?php if ($stat['changeType'] === 'increase'): ?>
                                    <svg class="icon-sm mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                    </svg>
                                <?php elseif ($stat['changeType'] === 'decrease'): ?>
                                    <svg class="icon-sm mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                <?php endif; ?>
                                <?= $stat['change'] ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
                <!-- Low Stock Alerts -->
                <div class="card">
                    <div class="card-header">
                        <div class="flex items-center justify-between">
                            <h2 class="card-title">Low Stock Alerts</h2>
                            <?php if (count($lowStockItems) > 0): ?>
                                <span class="badge badge-danger">
                                    <?= count($lowStockItems) ?> Alert<?= count($lowStockItems) > 1 ? 's' : '' ?>
                                </span>
                            <?php else: ?>
                                <?= getIconSvg('check-circle', 'icon text-green-500') ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="table-container">
                        <table class="overflow-x-auto">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Current</th>
                                    <th>Min Required</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($lowStockItems) > 0): ?>
                                    <?php foreach ($lowStockItems as $item): ?>
                                    <tr>
                                        <td class="font-semibold"><?= htmlspecialchars($item['product_name']) ?></td>
                                        <td><?= htmlspecialchars($item['category_name']) ?></td>
                                        <td class="text-red-500 font-medium"><?= $item['quantity'] ?></td>
                                        <td><?= $item['minimum_stock'] ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-gray-500 py-4">No low stock items found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Stock Movements -->
                <div class="card">
                    <div class="card-header">
                        <div class="flex items-center justify-between">
                            <h2 class="card-title">Recent Stock Movements</h2>
                            <?= getIconSvg('package', 'icon text-blue-500') ?>
                        </div>
                    </div>
                    <div class="table-container">
                        <table class="overflow-x-auto">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>Date</th>
                                    <th>User</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($recentMovements) > 0): ?>
                                    <?php foreach ($recentMovements as $movement): 
                                        $typeClass = $movement['update_type'] === 'increment' ? 'badge-success' : 'badge-danger';
                                        $typeText = $movement['update_type'] === 'increment' ? 'Received' : 'Dispatched';
                                    ?>
                                    <tr>
                                        <td class="font-semibold"><?= htmlspecialchars($movement['product_name']) ?></td>
                                        <td>
                                            <span class="badge <?= $typeClass ?>">
                                                <?= $typeText ?>
                                            </span>
                                        </td>
                                        <td><?= $movement['quantity'] ?></td>
                                        <td><?= date('Y-m-d', strtotime($movement['created_at'])) ?></td>
                                        <td><?= htmlspecialchars($movement['user_name']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-gray-500 py-4">No recent stock movements found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Stock by Category Chart -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Stock by Category</h2>
                    <?= getIconSvg('bar-chart-2', 'icon text-green-500') ?>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <?php if (count($stockByCategory) > 0): ?>
                            <?php 
                            $colors = ['blue', 'green', 'yellow', 'purple', 'indigo', 'pink', 'red'];
                            $colorIndex = 0;
                            $maxHeight = 150;
                            ?>
                            <?php foreach ($stockByCategory as $category): 
                                $height = ($category['total_items'] / $maxItems) * $maxHeight;
                                $color = $colors[$colorIndex % count($colors)];
                                $colorIndex++;
                            ?>
                            <div class="flex flex-col items-center">
                                <div class="chart-bar" style="height: <?= $height ?>px">
                                    <span class="chart-value"><?= number_format($category['total_items']) ?></span>
                                </div>
                                <span class="chart-label"><?= htmlspecialchars($category['category_name']) ?></span>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="w-full text-center text-gray-500">
                                No category data available
                            </div>
                        <?php endif; ?>
                    </div>
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