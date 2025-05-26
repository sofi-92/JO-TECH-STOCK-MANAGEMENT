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
            padding: 1.25rem;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
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
            min-width: 600px;
        }

        th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.875rem;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background-color: #f8fafc;
        }

        /* Tabs */
        .tabs {
           /*  border-bottom: 1px solid #e2e8f0; */
           gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .tab-button {
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #64748b;
         /*    border-bottom: 2px solid transparent; */
            margin-bottom: -1px;
            transition: all 0.2s;
        }

        .tab-button:hover {
            color: var(--primary);
          /*   border-color: #e2e8f0; */
        }

        .tab-button.active {
            color: var(--primary);
            border-color: var(--primary);
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-success {
            background-color: #ecfdf5;
            color: #059669;
        }

        .badge-danger {
            background-color: #fef2f2;
            color: #dc2626;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        /* Charts */
        .chart-container {
            height: 200px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            gap: 1rem;
            padding: 1rem 0 2.5rem;
            overflow-x: auto;
        }

        .chart-bar {
            width: 4rem;
            min-width: 3rem;
            border-radius: 0.25rem 0.25rem 0 0;
            background: linear-gradient(to top, var(--primary), var(--primary-light));
            position: relative;
            transition: height 0.3s ease;
        }

        .chart-label {
            position: absolute;
            bottom: -1.75rem;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.75rem;
            font-weight: 500;
            white-space: nowrap;
        }

        .chart-value {
            position: absolute;
            top: -1.25rem;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* Alerts */
        .alert {
            padding: 0.875rem 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.875rem;
            border-left: 3px solid transparent;
        }

        .alert-error {
            background-color: #fef2f2;
            color: #dc2626;
            border-left-color: #dc2626;
        }

        /* Utility classes */
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 0.75rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-6 { margin-bottom: 1.5rem; }

        .p-4 { padding: 1rem; }
        .p-6 { padding: 1.5rem; }

        

        .flex { display: flex; }
        .flex-wrap { flex-wrap: wrap; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .gap-2 { gap: 0.5rem; }
        .gap-3 { gap: 0.75rem; }
        .gap-4 { gap: 1rem; }

        .text-sm { font-size: 0.875rem; }
        .text-base { font-size: 1rem; }
        .text-lg { font-size: 1.125rem; }
        .text-xl { font-size: 1.25rem; }
        .text-2xl { font-size: 1.5rem; }

        .font-medium { font-weight: 500; }
        .font-semibold { font-weight: 600; }
        .font-bold { font-weight: 700; }

        .text-gray-500 { color: var(--gray); }
        .text-gray-600 { color: #475569; }
        .text-red-500 { color: var(--danger); }
        .text-red-600 { color: #dc2626; }

        .hidden { display: none; }
        .whitespace-nowrap { white-space: nowrap; }

        /* Icons */
        .icon {
            width: 1rem;
            height: 1rem;
            stroke-width: 2;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .content-area {
                padding: 1rem;
            }
            
            .btn {
                padding: 0.5rem 0.75rem;
                font-size: 0.8125rem;
            }
            
            .tab-button {
                padding: 0.5rem 0.75rem;
                font-size: 0.8125rem;
            }
            
            .chart-container {
                justify-content: flex-start;
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            .chart-bar {
                width: 3rem;
                min-width: 2.5rem;
            }
        }

        @media (max-width: 640px) {
            .flex-wrap > * {
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'header.php'; ?>
        
        <main class="content-area">
            <!-- Error Message -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span><?= $_SESSION['error'] ?></span>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Header Card -->
            <div class="card mb-6 p-6">
                <h1 class="text-2xl font-bold text-gray-800">Inventory Reports</h1>
                <p class="text-gray-600 mt-1">Generate detailed inventory reports and export data</p>
            </div>

            <!-- Main Report Card -->
            <div class="card p-6 mb-6">
                <!-- Tabs -->
                <div class="tabs flex overflow-x-auto mb-6">
                    <button onclick="showTab('stock-levels')" id="stock-levels-tab" class="tab-button active">
                        Stock Levels
                    </button>
                    <button onclick="showTab('low-stock')" id="low-stock-tab" class="tab-button">
                        Low Stock
                    </button>
                    <button onclick="showTab('category')" id="category-tab" class="tab-button">
                        Category Reports
                    </button>
                </div>

                <!-- Export Buttons -->
                <div class="flex flex-wrap gap-3 mb-6">
                    <a href="?export=stock_levels" class="btn btn-primary">
                        <svg class="icon mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Export Stock Levels
                    </a>
                    <a href="?export=low_stock" class="btn btn-primary">
                        <svg class="icon mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Export Low Stock
                    </a>
                    <a href="?export=category" class="btn btn-primary">
                        <svg class="icon mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Export Category Report
                    </a>
                </div>

                <!-- Stock Levels Tab Content -->
                <div id="stock-levels-content" class="tab-content">
                    <div class="table-container">
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
                                    $statusClass = $item['status'] === 'Low Stock' ? 'badge-danger' : 'badge-success';
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                                    <td><?= htmlspecialchars($item['category_name']) ?></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td><?= $item['minimum_stock'] ?></td>
                                    <td>
                                        <span class="badge <?= $statusClass ?>">
                                            <?= $item['status'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Low Stock Tab Content -->
                <div id="low-stock-content" class="tab-content hidden">
                    <div class="table-container">
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
                                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                                    <td><?= htmlspecialchars($item['category_name']) ?></td>
                                    <td class="text-red-600 font-medium"><?= $item['quantity'] ?></td>
                                    <td><?= $item['minimum_stock'] ?></td>
                                    <td class="text-red-600 font-medium"><?= $item['shortage'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Category Report Tab Content -->
                <div id="category-content" class="tab-content hidden">
                    <h3 class="text-lg font-semibold mb-4">Category Summary</h3>
                    <div class="chart-container">
                        <?php foreach ($categoryReportData as $category): 
                            $height = $maxItems > 0 ? ($category['total_items'] / $maxItems) * 150 : 0;
                        ?>
                        <div class="flex flex-col items-center">
                            <div class="chart-bar" style="height: <?= $height ?>px">
                                <span class="chart-value"><?= $category['total_items'] ?></span>
                            </div>
                            <span class="chart-label"><?= htmlspecialchars($category['category_name']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="table-container mt-6">
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
                                    <td><?= htmlspecialchars($category['category_name']) ?></td>
                                    <td><?= $category['total_items'] ?></td>
                                    <td><?= $category['low_stock_items'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="bg-gray-50 font-semibold">
                                    <td>Total</td>
                                    <td><?= $categoryTotals['total_items'] ?></td>
                                    <td><?= $categoryTotals['low_stock_items'] ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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
        document.querySelectorAll('.tab-button').forEach(tab => {
            tab.classList.remove('active');
        });
        
        document.getElementById(tabName + '-tab').classList.add('active');
    }

    // Initialize with first tab shown
    document.addEventListener('DOMContentLoaded', function() {
        showTab('stock-levels');
    });
    </script>
</body>
</html>

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