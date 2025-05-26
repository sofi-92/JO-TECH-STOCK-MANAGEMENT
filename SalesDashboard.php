<?php
session_start();
require_once 'config.php';
/* require_once 'sidebar.php'; */
$title = "Sales Dashboard";

// Fetch categories from database
$categories = [];
try {
    $stmt = $conn->prepare("SELECT category_id, category_name FROM categories");
    $stmt->execute();
    $result = $stmt->get_result();
    $categories = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (Exception $e) {
    error_log("Error fetching categories: " . $e->getMessage());
}

// Fetch products with category names and stock status
$products = [];
$totalProducts = 0;
$inStockCount = 0;
$lowStockCount = 0;
$outOfStockCount = 0;

try {
    // Base query
    $query = "SELECT p.product_id, p.product_name, c.category_name, p.quantity, p.minimum_stock 
              FROM products p
              JOIN categories c ON p.category_id = c.category_id";

    // Execute query
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Calculate stock status
    $totalProducts = count($products);
    foreach ($products as $product) {
        if ($product['quantity'] == 0) {
            $outOfStockCount++;
        } elseif ($product['quantity'] <= $product['minimum_stock']) {
            $lowStockCount++;
        } else {
            $inStockCount++;
        }
    }

} catch (Exception $e) {
    error_log("Error fetching products: " . $e->getMessage());
}

// Get category distribution
$categoryDistribution = [];
try {
    $stmt = $conn->prepare("
        SELECT c.category_name, COUNT(p.product_id) as product_count
        FROM products p
        JOIN categories c ON p.category_id = c.category_id
        GROUP BY c.category_name
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    $categoryDistribution = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (Exception $e) {
    error_log("Error fetching category distribution: " . $e->getMessage());
}

// Calculate percentages
$inStockPercent = $totalProducts > 0 ? round(($inStockCount / $totalProducts) * 100) : 0;
$lowStockPercent = $totalProducts > 0 ? round(($lowStockCount / $totalProducts) * 100) : 0;
$outOfStockPercent = $totalProducts > 0 ? round(($outOfStockCount / $totalProducts) * 100) : 0;
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

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f1f5f9;
            color: #334155;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        /* Dashboard Layout */
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
            padding: 1rem;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            margin-bottom: 1.5rem;
        }

        .card-header {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .card-body {
            padding: 1rem;
        }

        /* Tables */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        .table th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.75rem 1rem;
            text-align: left;
        }

        .table td {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.875rem;
        }

        .table tr:last-child td {
            border-bottom: none;
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

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #dc2626;
        }

        /* Forms */
        .form-control {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .w-fit{width: fit-content;}

        /* Progress bars */
        .progress-container {
            width: 100%;
            background-color: #e2e8f0;
            border-radius: 0.25rem;
            height: 0.5rem;
            margin-top: 0.25rem;
        }

        .progress-bar {
            height: 100%;
            border-radius: 0.25rem;
            background-color: var(--primary);
        }

        /* Stats */
        .stat-circle {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
            margin: 0 auto;
        }

        /* Quick Actions */
        .quick-action {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            transition: all 0.2s;
            width: 100%;
            background: white;
            margin-bottom: 0.75rem;
        }

        .quick-action:hover {
            background-color: #f8fafc;
            border-color: var(--primary-light);
        }

        /* Icons */
        .icon {
            width: 1.25rem;
            height: 1.25rem;
            stroke-width: 2;
        }

        .icon-sm {
            width: 1rem;
            height: 1rem;
        }

        /* Utility classes */
        .flex {
            display: flex;
        }

        .flex-col {
            flex-direction: column;
        }

        .items-center {
            align-items: center;
        }

        .justify-between {
            justify-content: space-between;
        }

        .gap-2 {
            gap: 0.5rem;
        }

        .gap-3 {
            gap: 0.75rem;
        }

        .gap-4 {
            gap: 1rem;
        }

        .mb-2 {
            margin-bottom: 0.5rem;
        }

        .mb-3 {
            margin-bottom: 0.75rem;
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        .mb-6 {
            margin-bottom: 1.5rem;
        }

        .p-3 {
            padding: 0.75rem;
        }

        .p-4 {
            padding: 1rem;
        }

        .text-sm {
            font-size: 0.875rem;
        }

        .text-base {
            font-size: 1rem;
        }

        .text-lg {
            font-size: 1.125rem;
        }

        .font-medium {
            font-weight: 500;
        }

        .font-semibold {
            font-weight: 600;
        }

        .text-gray-500 {
            color: var(--gray);
        }

        .text-gray-600 {
            color: #475569;
        }

        .text-center {
            text-align: center;
        }

        .whitespace-nowrap {
            white-space: nowrap;
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
            grid-template-columns: repeat(2, 1fr);
        }

        .grid-cols-3 {
            grid-template-columns: repeat(3, 1fr);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .content-area {
                padding: 0.75rem;
            }

            .card-header, .card-body {
                padding: 0.75rem;
            }

            .table th, .table td {
                padding: 0.75rem;
            }

            .grid-cols-2, .grid-cols-3 {
                grid-template-columns: 1fr;
            }

            .stat-circle {
                width: 3rem;
                height: 3rem;
                font-size: 0.875rem;
            }
        }

        @media (max-width: 480px) {
            .flex-col-xs {
                flex-direction: column;
            }
            
            .gap-xs-2 {
                gap: 0.5rem;
            }
        }
        
        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }
        .pl-10{
            padding-left: 2.5rem;
        }
    </style>
</head>
<body class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'header.php'; ?>
        
        <main class="content-area">
            <!-- Welcome Card -->
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-semibold">Welcome to the Sales Dashboard</h2>
                    <p class="text-gray-600 text-sm">As a sales team member, you can view current stock levels to inform customers about product availability.</p>
                </div>
            </div>

            <!-- Product Inventory Card -->
            <div class="card">
                <div class="card-header flex flex-col xs:flex-row justify-between items-start gap-xs-2">
                    <h3 class="text-lg font-semibold">Product Inventory</h3>
                    <div class="flex flex-row gap-2 w-full xs:w-auto">
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="search-icon icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" id="productSearch" class="form-control pl-10" placeholder="Search products...">
                        </div>
                        <select id="categoryFilter" class="form-control w-fit">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category['category_name']) ?>">
                                <?= htmlspecialchars($category['category_name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Stock Level</th>
                                    <th>Minimum Stock</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="productTable">
                                <?php foreach ($products as $product): 
                                    $statusClass = $product['quantity'] == 0 ? 'badge-danger' :
                                                  ($product['quantity'] <= $product['minimum_stock'] ? 'badge-warning' : 'badge-success');
                                    $statusText = $product['quantity'] == 0 ? 'Out of Stock' :
                                                 ($product['quantity'] <= $product['minimum_stock'] ? 'Low Stock' : 'In Stock');
                                ?>
                                <tr class="product-row">
                                    <td><?= htmlspecialchars($product['product_name']) ?></td>
                                    <td><?= htmlspecialchars($product['category_name']) ?></td>
                                    <td><?= $product['quantity'] ?></td>
                                    <td><?= $product['minimum_stock'] ?></td>
                                    <td>
                                        <span class="badge <?= $statusClass ?>">
                                            <?= $statusText ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-header">
                    <p class="text-sm text-gray-500">
                        Showing <span class="font-semibold">1</span> to <span class="font-semibold"><?= count($products) ?></span> of <span class="font-semibold"><?= $totalProducts ?></span> results
                    </p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Popular Categories -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold">Popular Categories</h3>
                    </div>
                    <div class="card-body space-y-3">
                        <?php foreach ($categoryDistribution as $category): 
                            $percentage = $totalProducts > 0 ? round(($category['product_count'] / $totalProducts) * 100) : 0;
                        ?>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span><?= htmlspecialchars($category['category_name']) ?></span>
                                <span class="font-semibold"><?= $percentage ?>%</span>
                            </div>
                            <div class="progress-container">
                                <div class="progress-bar" style="width: <?= $percentage ?>%"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Stock Availability -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold">Stock Availability</h3>
                    </div>
                    <div class="card-body">
                        <div class="grid grid-cols-3 gap-2">
                            <div class="text-center">
                                <div class="stat-circle bg-green-100 text-green-800">
                                    <?= $inStockPercent ?>%
                                </div>
                                <p class="mt-2 text-sm text-gray-500">In Stock</p>
                            </div>
                            <div class="text-center">
                                <div class="stat-circle bg-yellow-100 text-yellow-800">
                                    <?= $lowStockPercent ?>%
                                </div>
                                <p class="mt-2 text-sm text-gray-500">Low Stock</p>
                            </div>
                            <div class="text-center">
                                <div class="stat-circle bg-red-100 text-red-800">
                                    <?= $outOfStockPercent ?>%
                                </div>
                                <p class="mt-2 text-sm text-gray-500">Out of Stock</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold">Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <button class="quick-action">
                            <span>Check product availability</span>
                            <svg class="icon icon-sm text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                        <button class="quick-action">
                            <span>Filter by category</span>
                            <svg class="icon icon-sm text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                        </button>
                        <button class="quick-action">
                            <span>Contact warehouse</span>
                            <svg class="icon icon-sm text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
    // Product search and filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('productSearch');
        const categoryFilter = document.getElementById('categoryFilter');
        const productRows = document.querySelectorAll('.product-row');

        function filterProducts() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedCategory = categoryFilter.value.toLowerCase();

            productRows.forEach(row => {
                const productName = row.cells[0].textContent.toLowerCase();
                const category = row.cells[1].textContent.toLowerCase();
                const matchesSearch = productName.includes(searchTerm);
                const matchesCategory = selectedCategory === '' || category === selectedCategory;

                row.style.display = (matchesSearch && matchesCategory) ? '' : 'none';
            });
        }

        searchInput.addEventListener('input', filterProducts);
        categoryFilter.addEventListener('change', filterProducts);
    });
    </script>
</body>
</html>


<?php
function getIconSvg($iconName, $classes = '') {
    $icons = [
        'search' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
        'filter' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>',
        'box' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>'
    ];
    
    return $icons[$iconName] ?? '';
}
?>