<?php
session_start();
require_once 'config.php';
$title = "Reports";

// Check authentication
if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

// Handle CSV exports
if (isset($_GET['export'])) {
    $type = $_GET['export'];
    
    try {
        switch ($type) {
            case 'stock_levels':
                $data = getStockLevelsData();
                exportCSV($data, 'stock_levels.csv');
                break;
                
            case 'low_stock':
                $data = getLowStockData();
                exportCSV($data, 'low_stock.csv');
                break;
                
            case 'category':
                $data = getCategoryReportData();
                exportCSV($data, 'category_report.csv');
                break;
                
            default:
                throw new Exception("Invalid export type");
        }
        exit;
    } catch (Exception $e) {
        error_log("Export error: " . $e->getMessage());
        $_SESSION['error'] = "Error generating export";
        header('Location: reports.php');
        exit;
    }
}

// Get stock levels data
function getStockLevelsData() {
    global $conn;
    $stmt = $conn->prepare("
        SELECT p.product_name, c.category_name, p.quantity, p.minimum_stock
        FROM products p
        JOIN categories c ON p.category_id = c.category_id
        ORDER BY c.category_name, p.product_name
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    // Add status column
    foreach ($data as &$row) {
        $row['status'] = $row['quantity'] < $row['minimum_stock'] ? 'Low Stock' : 'In Stock';
    }
    return $data;
}

// Get low stock data
function getLowStockData() {
    global $conn;
    $stmt = $conn->prepare("
        SELECT p.product_name, c.category_name, p.quantity, p.minimum_stock
        FROM products p
        JOIN categories c ON p.category_id = c.category_id
        WHERE p.quantity < p.minimum_stock
        ORDER BY c.category_name, p.product_name
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    foreach ($data as &$row) {
        $row['shortage'] = $row['minimum_stock'] - $row['quantity'];
    }
    return $data;
}

// Get category report data
function getCategoryReportData() {
    global $conn;
    $stmt = $conn->prepare("
        SELECT 
            c.category_name,
            COUNT(p.product_id) AS total_items,
            SUM(CASE WHEN p.quantity < p.minimum_stock THEN 1 ELSE 0 END) AS low_stock_items
        FROM categories c
        LEFT JOIN products p ON c.category_id = p.category_id
        GROUP BY c.category_name
        ORDER BY c.category_name
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $data;
}

// CSV Export function
function exportCSV($data, $filename) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // Add header row
    if (!empty($data)) {
        fputcsv($output, array_keys($data[0]));
    }
    
    // Add data rows
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    
    fclose($output);
}

// Fetch data for display
try {
    $stockLevelsData = getStockLevelsData();
    $lowStockData = getLowStockData();
    $categoryReportData = getCategoryReportData();
    
    // Calculate totals for category summary
    $categoryTotals = [
        'total_items' => array_sum(array_column($categoryReportData, 'total_items')),
        'low_stock_items' => array_sum(array_column($categoryReportData, 'low_stock_items'))
    ];
    
} catch (Exception $e) {
    error_log("Report error: " . $e->getMessage());
    $_SESSION['error'] = "Error generating reports";
}

// Get categories for filters
$categories = [];
try {
    $stmt = $conn->prepare("SELECT category_name FROM categories ORDER BY category_name");
    $stmt->execute();
    $result = $stmt->get_result();
    $categories = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (Exception $e) {
    error_log("Category fetch error: " . $e->getMessage());
}

// Calculate chart data
$maxItems = 0;
if (!empty($categoryReportData)) {
    $maxItems = max(array_column($categoryReportData, 'total_items'));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - JO TECH</title>
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
        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background-color: #f9fafb;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        tr:hover {
            background-color: #f1f5f9;
        }
        /* Utility classes */
        .bg-white { background: #fff; }
        .bg-gray-50 { background: #f9fafb; }
        .bg-green-100 { background: #d1fae5; }
        .bg-red-100 { background: #fee2e2; }
        .bg-blue-600 { background: #3b82f6; }
        .text-gray-600 { color: #4b5563; }
        .font-semibold { font-weight: 600; }
        .rounded-lg { border-radius: 0.5rem; }
        .shadow-lg { box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); }
        .p-6 { padding: 1.5rem; }
        .flex { display: flex; }
        .space-x-4 > :not(:last-child) { margin-right: 1rem; }
        .hidden { display: none; }
        .whitespace-nowrap { white-space: nowrap; }
    </style>
</head>
<body class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'header.php'; ?>
        
        <main class="content-area">
            <!-- Display messages -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <?= $_SESSION['error'] ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 gap-6 mb-6">
                <div class="bg-white p-6 shadow-lg rounded-xl">
                    <h2 class="text-2xl font-bold text-gray-800">Inventory Reports</h2>
                    <p class="text-gray-600 mt-2">Generate detailed inventory reports and export data</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg mb-6">
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

                <div class="flex space-x-4 mb-4">
                    <a href="?export=stock_levels" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <?= getIconSvg('download', 'h-4 w-4 mr-2') ?>
                        Export Stock Levels
                    </a>
                    <a href="?export=low_stock" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <?= getIconSvg('download', 'h-4 w-4 mr-2') ?>
                        Export Low Stock
                    </a>
                    <a href="?export=category" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <?= getIconSvg('download', 'h-4 w-4 mr-2') ?>
                        Export Category Report
                    </a>
                </div>

                <!-- Stock Levels Tab Content -->
                <div id="stock-levels-content" class="tab-content">
                    <table>
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Minimum Stock</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stockLevelsData as $item): 
                                $statusClass = $item['status'] === 'Low Stock' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800';
                            ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($item['product_name']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($item['category_name']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= $item['quantity'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= $item['minimum_stock'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded-full text-sm <?= $statusClass ?>">
                                        <?= $item['status'] ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Low Stock Tab Content -->
                <div id="low-stock-content" class="tab-content hidden">
                    <table>
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Minimum Stock</th>
                                <th>Shortage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lowStockData as $item): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($item['product_name']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($item['category_name']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-red-600"><?= $item['quantity'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= $item['minimum_stock'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-red-600"><?= $item['shortage'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Category Report Tab Content -->
                <div id="category-content" class="tab-content hidden">
                    <h3 class="text-lg font-semibold mb-4">Category Summary</h3>
                    <div id="bar-chart" class="h-64 flex items-end justify-around gap-4 mb-4">
                        <?php foreach ($categoryReportData as $category): 
                            $height = $maxItems > 0 ? ($category['total_items'] / $maxItems) * 150 : 0;
                        ?>
                        <div class="flex flex-col items-center">
                            <div class="bg-blue-500 w-24 rounded-t chart-bar" 
                                 style="height: <?= $height ?>px"></div>
                            <p class="mt-2 text-sm font-medium"><?= htmlspecialchars($category['category_name']) ?></p>
                            <p class="text-xs text-gray-500"><?= $category['total_items'] ?> items</p>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Category Name</th>
                                <th>Total Items</th>
                                <th>Low Stock Items</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categoryReportData as $category): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($category['category_name']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= $category['total_items'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= $category['low_stock_items'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="bg-gray-50 font-semibold">
                                <td class="px-6 py-4 whitespace-nowrap">Total</td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= $categoryTotals['total_items'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= $categoryTotals['low_stock_items'] ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

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

// Initialize with first tab shown
document.addEventListener('DOMContentLoaded', function() {
    showTab('stock-levels');
});
</script>

<?php
function getIconSvg($iconName, $classes = '') {
    $icons = [
        'download' => '<svg class="'.$classes.'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>',
        'file-text' => '<svg class="'.$classes.'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
        // Add other icons as needed
    ];
    return $icons[$iconName] ?? '';
}
?>
</body>
</html>