<?php
session_start();
require_once 'config.php';
require_once 'sidebar.php';
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
    <title><?= htmlspecialchars($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f9fafb; /* Light gray background */
        }
    </style>
</head>
<body>

<div class="container mx-auto p-6">
    <div class="grid grid-cols-1 gap-6 mb-6">
        <div class="bg-white p-4 shadow-sm rounded-lg">
            <h2 class="text-lg font-semibold mb-4">Welcome to the Sales Dashboard</h2>
            <p class="text-gray-600">
                As a sales team member, you can view current stock levels to inform customers about product availability.
            </p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <h2 class="text-lg font-semibold mb-4 md:mb-0">Product Inventory</h2>
            <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <?= getIconSvg('search', 'h-4 w-4 text-gray-400') ?>
                    </div>
                    <input type="text" id="productSearch" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Search products...">
                </div>
                <div class="relative inline-block text-left">
                    <select id="categoryFilter" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category['category_name']) ?>">
                            <?= htmlspecialchars($category['category_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Minimum Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="productTable">
                    <?php foreach ($products as $product): 
                        $statusClass = $product['quantity'] == 0 ? 'bg-red-100 text-red-800' :
                                      ($product['quantity'] <= $product['minimum_stock'] ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800');
                        $statusText = $product['quantity'] == 0 ? 'Out of Stock' :
                                     ($product['quantity'] <= $product['minimum_stock'] ? 'Low Stock' : 'In Stock');
                    ?>
                    <tr class="product-row">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($product['product_name']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($product['category_name']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $product['quantity'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $product['minimum_stock'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusClass ?>">
                                <?= $statusText ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-between mt-6">
            <div class="text-sm text-gray-500">
                Showing <span class="font-medium">1</span> to <span class="font-medium"><?= count($products) ?></span> of <span class="font-medium"><?= $totalProducts ?></span> results
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h3 class="font-semibold text-gray-800 mb-4">Popular Categories</h3>
            <div class="space-y-4">
                <?php foreach ($categoryDistribution as $category): 
                    $percentage = $totalProducts > 0 ? round(($category['product_count'] / $totalProducts) * 100) : 0;
                ?>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600"><?= htmlspecialchars($category['category_name']) ?></span>
                    <div class="w-2/3 bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: <?= $percentage ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h3 class="font-semibold text-gray-800 mb-4">Stock Availability</h3>
            <div class="flex items-center justify-around">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center p-4 bg-green-100 rounded-full">
                        <span class="text-xl font-bold text-green-800"><?= $inStockPercent ?>%</span>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">In Stock</p>
                </div>
                <div class="text-center">
                    <div class="inline-flex items-center justify-center p-4 bg-yellow-100 rounded-full">
                        <span class="text-xl font-bold text-yellow-800"><?= $lowStockPercent ?>%</span>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Low Stock</p>
                </div>
                <div class="text-center">
                    <div class="inline-flex items-center justify-center p-4 bg-red-100 rounded-full">
                        <span class="text-xl font-bold text-red-800"><?= $outOfStockPercent ?>%</span>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Out of Stock</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h3 class="font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="space-y-4">
                <button class="w-full flex items-center justify-between px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <span>Check product availability</span>
                    <?= getIconSvg('search', 'h-4 w-4') ?>
                </button>
                <button class="w-full flex items-center justify-between px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <span>Filter by category</span>
                    <?= getIconSvg('filter', 'h-4 w-4') ?>
                </button>
                <button class="w-full flex items-center justify-between px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <span>Contact warehouse</span>
                    <?= getIconSvg('box', 'h-4 w-4') ?>
                </button>
            </div>
        </div>
    </div>
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
</body>
</html>